#！/bin/bash
#echo "加载中..."
#for var in $*
#do
  #cp -r D:\/wordpress\/wordpress D:\/wordpress\/${var}
#done

#set-executionpolicy remotesigned
Import-Module WebAdministration
$sitePort=80
echo $sitePort
exit
$SiteName = $1
$SiteAppPools = "DefaultAppPool"
$SiteAppPoolsModel = "经典"
$AppPoolType = "LocalSystem"
$managedRuntimeVersion = "v4.0"
$WebSitePath = "D:\/wordpress\/$1"
$HostHeader1 = $1
$HostHeader2 = $1
$defaultDocument = "index.php"
$IISLogFile = "D:\/wordpress\/wordpress\/$SiteName"
$net32Or64 = $true

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


function CreatIISLogFile(){
    .$Env:windir\system32\inetsrv\appcmd.exe set site $SiteName "-logfile.directory:$IISLogFile"
}


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
