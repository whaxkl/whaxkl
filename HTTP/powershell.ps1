Import-Module WebAdministration
#新建应用程序池 api.dd.com
#New-Item iis:\AppPools\api.dd.com
Set-ItemProperty iis:\AppPools\$1 managedRuntimeVersion v4.0 #更改应用程序池版本为4.0，默认为2.0（Windows Server 2008 R2）
#新建站点 api.dd.com，主机头为 api.dd.com，路经为 d:\apidd
New-Item iis:\Sites\$1 -bindings @{protocol="http";bindingInformation=":80:$1"} -physicalPath d:\wordpress\$1
#为站点 api.dd.com 添加主机头 imageapi.dd2.com
New-WebBinding -Name "$1" -IPAddress "*" -Port 80 -HostHeader image$1
#为站点 api.dd.com 更改应用程序池为 api.dd.com
Set-ItemProperty IIS:\Sites\$1 -name applicationPool -value DefaultAppPool
#在站点api.dd.com下新建应用程序cust_account_api ，目录为D:\cust_account_api_new
new-item iis:\sites\$1\cust_account_api -type Application -physicalpath D:\wordpress\$1\cust_account_api_new
Set-ItemProperty IIS:\Sites\$1\cust_account_api -name applicationPool -value $1
#在站点ServerLog下新建虚拟目录cust_account_api ，目录为D:\cust_account_api_new\log
new-item D:\wordpress\$1\cust_account_api_new\log -type directory -force
new-item iis:\sites\ServerLog\cust_account_api -type VirtualDirectory -physicalpath D:\wordpress\$1\cust_account_api_new\log