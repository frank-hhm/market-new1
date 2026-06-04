##
安装 进程守护管理器
## 手续费返现 每天的 18:00 执行一次
php think finance:member:fee

## 文章 每天0点开始，每隔2小时的第30分钟执行一次
访问URL-[ http://www.yxraf.com/api/common.publics/gather ]

## 进程常驻 SetKDataJob
php think queue:listen --queue SetKDataJob

## 进程常驻 版本更新 VersionPublishJob
php think queue:listen --queue VersionPublishJob

## 进程常驻 平仓记录 OrderPingCangLogJob
php think queue:listen --queue OrderPingCangLogJob

## 执行会员默认自选  废除
php think set:member:default

## 服务启动  必须
php easyswoole.php server start
php easyswoole.php server stop
php easyswoole.php server start -d

# 其他
netstat -anp | grep 9501

kill -9 端口

## 开发环境执行版本推送
php think publish:version

#其他
更新 composer
composer self-update
composer self-update --latest
切换官方源
composer config --unset repos.packagist

##根路径执行
##使用场景：挂载一个空卷
mkdir /market
df -Th

lsblk
## 确定卷上是否有文件系统,没有对应文件系统无法挂载
sudo file -s /dev/nvme1n1p1

sudo mkfs -t ext4 /dev/nvme1n1p1

##挂载
sudo mount /dev/nvme1n1p1 /market
df -h /market

sudo blkid /dev/nvme1n1p1


##配置完成，可reboot重启服务器测试


1. 确认SSH服务已安装并运行
   首先，确保你的系统上已经安装了OpenSSH服务器，并且服务正在运行。

sudo systemctl status sshd
如果没有运行，请启动它：

sudo systemctl start sshd
并且设置开机自启：

sudo systemctl enable sshd
2. 修改SSH配置文件以允许root登录
sudo vi /etc/ssh/sshd_config
在文件中找到以下两行，并确保它们如下所示（取消注释或修改）：
深色版本
#PermitRootLogin prohibit-password
# 改为：
PermitRootLogin yes

#PasswordAuthentication no
# 改为：
PasswordAuthentication yes
保存并退出编辑器。

3. 设置或更改root密码
sudo passwd root

4. 重启SSH服务使更改生效
sudo systemctl restart sshd

mysql -u root -p
CREATE USER 'newuser'@'%' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON *.* TO 'newuser'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;

sudo yum install git -y

UPDATE table_name
SET column_name = REPLACE(column_name, '要被替换的字符串', '替换成的字符串')
WHERE 条件;


{
"ac":"auth",
"params":"7952b98579fd478d9e49b6b3de82f4c99f17e889d5eb4fa6bdbba0238fc22861"
}
{
"ac":"subscribe",
"params":"XAUUSD",
"types":"quote"
}
删除journalctl日志
journalctl --rotate
journalctl --vacuum-time=1s
sudo journalctl -u sshd | grep "root"
删除wtmp日志
[ -e /var/log/wtmp ] && > /var/log/wtmp
删除btmp日志
[ -e /var/log/btmp ] && > /var/log/btmp
删除lastlog日志
[ -e /var/log/lastlog ] && > /var/log/lastlog
删除auth.log日志
sed -i '/xxx.xxx.xxx.xxx/d' /var/log/auth.log
清除bash记录
history -r



#UPDATE m_finance_member_recharge SET voucher_url = REPLACE(voucher_url, '//www.liangdian1.cc/', '//ld789.cc/');


#UPDATE m_follow_person SET avatar = REPLACE(avatar, '//www.liangdian1.cc/', '//ld789.cc/');

#UPDATE m_media SET file_domain = REPLACE(file_domain, '//www.liangdian1.cc/', '//ld789.cc/');

#UPDATE m_member SET avatar = REPLACE(avatar, '//www.liangdian1.cc/', '//ld789.cc/');

#UPDATE m_notice SET content = REPLACE(content, '//www.liangdian1.cc/', '//ld789.cc/');

#UPDATE m_system_admin SET avatar = REPLACE(avatar, '//www.liangdian1.cc/', '//ld789.cc/');

--
-- UPDATE m_system_config
-- SET sys_value = REPLACE(sys_value, '//www.liangdian1.cc/', '//ld789.cc/');

UPDATE m_payment SET cover = REPLACE(cover, '//www.liangdian1.cc/', '//ld789.cc/');