#echo "正在创建..."
#for var in $*
#do
  #cp -r D:\/wordpress\/wordpress D:\/wordpress\/${var}
#done

# ************************************************************
# *                                                          *
# *                 Powershell部署IIS脚本                    *
# *                                                          *
# ************************************************************
# 作者:网上管家婆运维工程师--Sanddy QQ:442405
# 日期：2014-06-02
#set-executionpolicy remotesigned
Import-Module WebAdministration  #导入IIS模块
# 建立IIS站点所用参数（修改以下参数可以建立不同的站点）
#################################################################################################
$sitePort = 80  #端口
$SiteName = $1 #站点名
$SiteAppPools = "DefaultAppPool"  #进程池名
$SiteAppPoolsModel = "经典"  #进程池使用通道模式
$AppPoolType = "LocalSystem"        #指定应用程序池要使用的帐户标识（0 >Local Service 1 >Local System  2 >Network Service  3 >User 4 >ApplicationPoolIdentity）
$managedRuntimeVersion = "v4.0"  #.net版本
$WebSitePath = "D:\/wordpress\/$1" #站点程序路径
$HostHeader1 = $1      #绑定站点域名
$HostHeader2 = $1      #绑定站点域名
$defaultDocument = "index.php"
$IISLogFile = "D:\/wordpress\/wordpress\/$SiteName" #IIS日志路径
$net32Or64 = $true  #是否启用.net32模式

#################################################################################################
#创建IIS应用程序池
function BuildAppPool(){
    $AppPool = "iis:\AppPools\" + $SiteAppPools
    $existAppPool = Test-Path $AppPool
    if($existAppPool -eq $false){
        #创建应用程序池
        .$Env:windir\system32\inetsrv\appcmd.exe add apppool /name:$SiteAppPools /managedRuntimeVersion:$managedRuntimeVersion  /managedPipelineMode:$SiteAppPoolsModel
        #指定应用程序池要使用的帐户标识
        .$Env:windir\system32\inetsrv\appcmd.exe set config /section:applicationPools /[name="'$SiteAppPools'"].processModel.identityType:NetworkService
        #设置应用程序池使用.net版本
        .$Env:windir\system32\inetsrv\appcmd.exe add apppool /name:$SiteAppPools /managedRuntimeVersion:$managedRuntimeVersion  /managedPipelineMode:$SiteAppPoolsModel
        #限制进程使用内存上限为1.5G
        .$Env:windir\system32\inetsrv\appcmd.exe set config /section:applicationPools /[name="'$SiteAppPools'"].recycling.periodicRestart.privateMemory:1572864
        #指定进程固定回收时间
        .$Env:windir\system32\inetsrv\appcmd.exe set apppool /apppool.name: $SiteAppPools /recycling.periodicRestart.time:1.00:00:00
        #启用.net32模式
        .$Env:windir\system32\inetsrv\appcmd.exe set config /section:applicationPools /[name="'$SiteAppPools'"].enable32BitAppOnWin64:$net32Or64
        #是否自动启动
        .$Env:windir\system32\inetsrv\appcmd.exe set config /section:applicationPools /[name="'$SiteAppPools'"].autoStart:$true
    }
}

#创建IIS应用站点
function BuildSite(){
    $appSitePath = "iis:\sites\"+$SiteName
    $existweb = Test-Path $appSitePath
    if(!$existweb)
    {
        New-Website -name $SiteName -port $sitePort  -ApplicationPool $SiteAppPools -PhysicalPath $WebSitePath
        .$Env:windir\system32\inetsrv\appcmd.exe set site $SiteName /bindings:"http/*:80:$HostHeader1,http/*:80:$HostHeader2"
        .$Env:windir\system32\inetsrv\appcmd.exe set config /section:directoryBrowse /enabled:false
    }
    else{
    echo "'$SiteName' is Already exists"
    }
}

#设置IIS日志记录路径
function CreatIISLogFile(){
    .$Env:windir\system32\inetsrv\appcmd.exe set site $SiteName "-logfile.directory:$IISLogFile"
}

#为F5设备创建ISPAI筛选器
function CreatISP(){
    $x = [string](.$Env:windir\system32\inetsrv\appcmd.exe list config $SiteName /section:isapiFilters)
    if ($x -like "*F5XForwardedFor*"){
    echo "isapiFilters is Already exists"
    }
    else{
    .$Env:windir\system32\inetsrv\appcmd.exe unlock config $SiteName "-section:system.webServer/isapiFilters"
    .$Env:windir\system32\inetsrv\appcmd.exe set config $SiteName /section:isapiFilters /"+[name='F5XForwardedFor',path='$Env:windir\System32\F5XForwardedFor.dll',enabled='true']"
    }
}

function RunBuild(){
    #BuildAppPool
    BuildSite
    CreatIISLogFile
    CreatISP
    .$Env:windir\system32\inetsrv\appcmd.exe start site $SiteName
}
RunBuild
