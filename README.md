# 基于 CAS 实现通用的单点登录

## Sso客户端

**客户端配置：** CAS 服务端一样，我们需要在 Laravel 应用中通过 CAS PHP 客户端与 CAS Server 进行通信。CAS 官方为 PHP 语言提供了客户端实现 phpCAS，此外，Laravel 生态也有基于 phpCAS 实现的扩展包 subfission/cas：composer require subfission/cas

1.在 config/app.php 中注册服务提供者和门面：

2.在 providers 中添加这个配置：\Subfission\Cas\CasServiceProvider::class,

3.在 aliases 中添加这个配置：'Cas' => \Subfission\Cas\Facades\Cas::class,

4.添加如下两个中间件到 app/Http/Kernel.php 的 $routeMiddleware 属性用于单点登录判断：

'cas.auth'  => \Subfission\Cas\Middleware\CASAuth::class,

'cas.guest' => \Subfission\Cas\Middleware\RedirectCASAuthenticated::class,

5.发布 CAS 客户端扩展包配置文件 cas.php 到 config 目录下：php artisan vendor:publish --provider="Subfission\Cas\CasServiceProvider"

然后修改 .env 环境配置，以便可以完成 CAS 客户端配置：

CAS_HOSTNAME=server.test //CAS 服务端域名

CAS_REAL_HOSTS=server.test

CAS_LOGOUT_URL=https://server.test/cas/logout //退出 URL

CAS_LOGOUT_REDIRECT=http://app.test //服务端退出

CAS_REDIRECT_PATH=http://app.test/user //登录后对应的客户端回跳地址

CAS_ENABLE_SAML=false //服务端不支持 SAML

在服务端 blog56 应用中修改 config/app.php 的 timezone 默认配置值：'timezone' => 'Asia/Shanghai',

## 在服务端注册客户端应用

在 cas_services 数据表中注册客户端严服务

在 cas_service_hosts 数据表中绑定上述服务对应域名

## 在服务端创建测试用户

php artisan tinker

(参考：https://laravelacademy.org/post/9775)