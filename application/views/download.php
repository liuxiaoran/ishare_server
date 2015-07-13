<?php
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/26
 * Time: 21:31
 */
if (is_file($file)) {
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=" . basename($file));
    readfile($file);
    exit;
} else {
    echo "文件不存在！";
    exit;
}
?>