# Laravel QingStor Storage

## 关于
Laravel QingStor 文件上传系统，基于<a href="https://github.com/yunify/qingstor-sdk-php">青云官方SDK</a>。
推荐使用<a href="https://github.com/zgldh/laravel-upload-manager">laravel-upload-manager</a>进行整合。

## 安装
- Composer 安装
    ```
    composer require gexingw/laravel-qingstor-storage
    ```
- 下载安装，下载解压缩即可

## 配置

- 配置青云的API Key和Secret
   
    ```php
            // 在config/filesystems.php disks数组添加如下代码
            'qingstor' => [
                'driver' => 'QingStor',
                'bucket' => 'My Bucket',
                'zone' => 'My Zone',
                'access_key_id' => 'Access ID', // 青云App Key
                'secret_access_key' => 'Access Secret', //青云App Secret
            ],
    ```

## 用法
   - 简单用法
        - 设置为默认上传策略
            将 config/filesystems.php default 值改为 "qingstor"
            ```php
            \Storage::put($path, $content);
            ```
        - 自定义上传策略
            ```php
            \Storage::disk('qingstor')->put($path, $content);
            ```
   - 更多用法请参考 <a href="https://laravel.com/docs/5.5/filesystem">Laravel官方文档</a>

