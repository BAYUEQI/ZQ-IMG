<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 处理预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config/database.php';

try {
    $db = Database::getInstance();
    $mysqli = $db->getConnection();
    
    if (!$mysqli) {
        throw new Exception('数据库连接失败');
    }
    
    // 获取查询参数
    $type = $_GET['type'] ?? 'random'; // random, latest, oldest
    $format = $_GET['format'] ?? 'json'; // json, redirect, jsonp
    $callback = $_GET['callback'] ?? '';
    $count = intval($_GET['count'] ?? 1); // 限制最多10张
    $category = $_GET['category'] ?? ''; // 可以按分类筛选（如果数据库有category字段）
    
    // 构建SQL查询 - 使用正确的字段名
    $sql = "SELECT id, url, path, storage, size, upload_ip, created_at FROM images WHERE 1=1";
    $params = [];
    $types = "";
    
    // 如果有分类筛选
    if (!empty($category)) {
        $sql .= " AND category = ?";
        $params[] = $category;
        $types .= "s";
    }
    
    // 根据类型排序
    switch ($type) {
        case 'latest':
            $sql .= " ORDER BY created_at DESC";
            break;
        case 'oldest':
            $sql .= " ORDER BY created_at ASC";
            break;
        case 'random':
        default:
            $sql .= " ORDER BY RAND()";
            break;
    }
    
    $sql .= " LIMIT ?";
    $params[] = $count;
    $types .= "i";
    
    // 准备并执行查询
    $stmt = $mysqli->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $images = [];
    while ($row = $result->fetch_assoc()) {
        // 格式化文件大小
        $size = formatFileSize($row['size']);
        
        $images[] = [
            'id' => $row['id'],
            'url' => $row['url'],
            'path' => $row['path'],
            'storage' => $row['storage'],
            'size' => $size,
            'size_bytes' => $row['size'],
            'upload_time' => $row['created_at'],
            'upload_ip' => $row['upload_ip']
        ];
    }
    
    // 构建响应数据
    $response = [
        'success' => true,
        'code' => 200,
        'message' => '获取成功',
        'data' => [
            'type' => $type,
            'count' => count($images),
            'total_available' => getTotalImageCount($mysqli),
            'images' => $images
        ],
        'timestamp' => date('Y-m-d H:i:s'),
        'api_version' => '1.0'
    ];
    
    // 根据格式返回不同响应
    switch ($format) {
        case 'redirect':
            if (!empty($images)) {
                $randomImage = $images[array_rand($images)];
                header('Location: ' . $randomImage['url']);
                exit;
            }
            break;
            
        case 'jsonp':
            if (!empty($callback)) {
                echo $callback . '(' . json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ')';
                exit;
            }
            // 如果没有callback参数，fallback到json
            break;
            
        case 'json':
        default:
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            break;
    }
    
} catch (Exception $e) {
    $errorResponse = [
        'success' => false,
        'code' => 500,
        'message' => '获取失败: ' . $e->getMessage(),
        'data' => null,
        'timestamp' => date('Y-m-d H:i:s'),
        'api_version' => '1.0'
    ];
    
    if ($format === 'jsonp' && !empty($callback)) {
        echo $callback . '(' . json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ')';
    } else {
        echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}

/**
 * 获取数据库中图片总数
 */
function getTotalImageCount($mysqli) {
    $result = $mysqli->query("SELECT COUNT(*) as total FROM images");
    return $result ? $result->fetch_assoc()['total'] : 0;
}

/**
 * 格式化文件大小
 */
function formatFileSize($sizeInBytes) {
    if ($sizeInBytes < 1024) {
        return $sizeInBytes . ' B';
    } elseif ($sizeInBytes < 1024 * 1024) {
        return number_format($sizeInBytes / 1024, 2) . ' KB';
    } elseif ($sizeInBytes < 1024 * 1024 * 1024) {
        return number_format($sizeInBytes / (1024 * 1024), 2) . ' MB';
    } else {
        return number_format($sizeInBytes / (1024 * 1024 * 1024), 2) . ' GB';
    }
}
?>
