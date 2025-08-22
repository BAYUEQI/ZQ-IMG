<?php
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
        http_response_code(500);
        echo '数据库连接失败';
        exit;
    }
    
    // 获取查询参数
    $format = $_GET['format'] ?? 'redirect'; // redirect, url, json
    $count = intval($_GET['count'] ?? 1); // 限制最多5张
    
    // 随机获取图片
    $stmt = $mysqli->prepare("SELECT url FROM images ORDER BY RAND() LIMIT ?");
    $stmt->bind_param("i", $count);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['url'];
    }
    
    if (empty($images)) {
        http_response_code(404);
        echo '没有找到图片';
        exit;
    }
    
    // 根据格式返回不同响应
    switch ($format) {
        case 'url':
            // 返回图片URL
            if ($count === 1) {
                echo $images[0];
            } else {
                echo json_encode($images);
            }
            break;
            
        case 'json':
            // 返回JSON格式
            header('Content-Type: application/json; charset=utf-8');
            $response = [
                'success' => true,
                'count' => count($images),
                'images' => $images
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            break;
            
        case 'redirect':
        default:
            // 重定向到随机图片
            $randomImage = $images[array_rand($images)];
            header('Location: ' . $randomImage);
            exit;
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo '获取失败: ' . $e->getMessage();
}
?>
