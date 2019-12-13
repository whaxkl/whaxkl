#echo "���ڴ���..."
#for var in $*
#do
  #cp -r D:\/wordpress\/wordpress D:\/wordpress\/${var}
#done

# ************************************************************
# *                                                          *
# *                 Powershell����IIS�ű�                    *
# *                                                          *
# ************************************************************
# ����:���Ϲܼ�����ά����ʦ--Sanddy QQ:442405
# ���ڣ�2014-06-02
#set-executionpolicy remotesigned
Import-Module WebAdministration  #����IISģ��
# ����IISվ�����ò������޸����²������Խ�����ͬ��վ�㣩
#################################################################################################
$sitePort = 80  #�˿�
$SiteName = $1 #վ����
$SiteAppPools = "DefaultAppPool"  #���̳���
$SiteAppPoolsModel = "����"  #���̳�ʹ��ͨ��ģʽ
$AppPoolType = "LocalSystem"        #ָ��Ӧ�ó����Ҫʹ�õ��ʻ���ʶ��0 >Local Service 1 >Local System  2 >Network Service  3 >User 4 >ApplicationPoolIdentity��
$managedRuntimeVersion = "v4.0"  #.net�汾
$WebSitePath = "D:\/wordpress\/$1" #վ�����·��
$HostHeader1 = $1      #��վ������
$HostHeader2 = $1      #��վ������
$defaultDocument = "index.php"
$IISLogFile = "D:\/wordpress\/wordpress\/$SiteName" #IIS��־·��
$net32Or64 = $true  #�Ƿ�����.net32ģʽ

#################################################################################################
#����IISӦ�ó����
function BuildAppPool(){
    $AppPool = "iis:\AppPools\" + $SiteAppPools
    $existAppPool = Test-Path $AppPool
    if($existAppPool -eq $false){
        #����Ӧ�ó����
        .$Env:windir\system32\inetsrv\appcmd.exe add apppool /name:$SiteAppPools /managedRuntimeVersion:$managedRuntimeVersion  /managedPipelineMode:$SiteAppPoolsModel
        #ָ��Ӧ�ó����Ҫʹ�õ��ʻ���ʶ
        .$Env:windir\system32\inetsrv\appcmd.exe set config /section:applicationPools /[name="'$SiteAppPools'"].processModel.identityType:NetworkService
        #����Ӧ�ó����ʹ��.net�汾
        .$Env:windir\system32\inetsrv\appcmd.exe add apppool /name:$SiteAppPools /managedRuntimeVersion:$managedRuntimeVersion  /managedPipelineMode:$SiteAppPoolsModel
        #���ƽ���ʹ���ڴ�����Ϊ1.5G
        .$Env:windir\system32\inetsrv\appcmd.exe set config /section:applicationPools /[name="'$SiteAppPools'"].recycling.periodicRestart.privateMemory:1572864
        #ָ�����̶̹�����ʱ��
        .$Env:windir\system32\inetsrv\appcmd.exe set apppool /apppool.name: $SiteAppPools /recycling.periodicRestart.time:1.00:00:00
        #����.net32ģʽ
        .$Env:windir\system32\inetsrv\appcmd.exe set config /section:applicationPools /[name="'$SiteAppPools'"].enable32BitAppOnWin64:$net32Or64
        #�Ƿ��Զ�����
        .$Env:windir\system32\inetsrv\appcmd.exe set config /section:applicationPools /[name="'$SiteAppPools'"].autoStart:$true
    }
}

#����IISӦ��վ��
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

#����IIS��־��¼·��
function CreatIISLogFile(){
    .$Env:windir\system32\inetsrv\appcmd.exe set site $SiteName "-logfile.directory:$IISLogFile"
}

#ΪF5�豸����ISPAIɸѡ��
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
