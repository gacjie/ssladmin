#####  源码简介：    
本程序用于自签SSL证书，拥有完善的根级CA证书、中级CA证书、多级CA证书、服务证书的签发。   
#####  演示图片：    
![ssladmin.jpg](https://raw.githubusercontent.com/gacjie/ssladmin/main/ssladmin.jpg)
##### 功能说明:    
 【签发机构】支持签发根CA证书，以及多级CA证书。    
 【签发证书】支持自定义CA证书签发服务证书。    
 【证书设置】支持设置，加密方式，CRT链接，CRL链接，CPS链接。   
注意事项：序列号只能填写00-64的数值，且同一签发机构，不要有相同序列号的证书，不然同时使用的时候会有问题。     
有效期天数请填写365 3650这样的数值，其他的可能会出错。    
##### 更新日志:    
 Build 211107   
 签发机构：支持签发根CA证书，以及多级CA证书。   
 签发证书：支持自定义CA证书签发服务证书。   
 证书设置：支持设置，加密方式，CRT链接，CRL链接，CPS链接。    
##### 安装说明：   
1.php开启FileInfo zlib扩展。    
  zlib设置方法：    
  打开php.ini配置文件修改以下配置字段    
  zlib.output_compression = On    
  zlib.output_compression_level = 1    
  然后重载配置文件即可    
2.设置thinkphp伪静态。     
3.设置public为运行目录，并取消防跨站。    
4.修改runtime文件夹权限777    
5.访问http#//域名/install.php安装   
按照完成后可删除以下文件。    
public/database.sql    
public/install.php    
未使用apache可删除以下文件    
public/.htaccess   
