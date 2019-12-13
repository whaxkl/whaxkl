#！/bin/bash
try
{
    Import-Module WebAdministration -ErrorAction Stop
}catch [System.SystemException]
{
    Write-Host -foregroundColor "Red" "请先安装IIS管理脚本和工具："
    Write-Host -foregroundColor "Red" "Win2008 *,角色-->添加角色--->功能工具下面的'IIS管理脚本和工具'"
    Write-Host -foregroundColor "Red" "Win7 在卸载程序中，点击'打开或关闭Windows功能'"
    break
}
 
function CreateWebSite([string]$siteName,[string]$physicalPath,[string]$ports)
{
    if(GetSite $siteName)
    {
        #todo: 待优化
        Write-Host "站点已经存在"
        return
    }
    $bindings = CheckBindingInfo $ports
    try
    {
        $site = New-Item IIS:\Sites\$siteName -bindings $bindings -physicalPath $physicalPath -ErrorAction Stop
        #todo: 待优化        
        $site.enabledProtocols = "http,net.tcp"
    }catch [System.SystemException]
    {
        Write-Host "创建站点失败"
        break
    }
    #todo: 待优化
    CreateAppPool $siteName
    Set-ItemProperty IIS:\Sites\$siteName -name applicationPool -value $siteName
    return $site
}
 
function CreateApplication([string]$siteName,[string]$appName,[string]$appPhysPath)
{
    #todo: 待优化
    if(GetApplication $siteName $appName)
    {
        Write-Host "应用程序已经存在"
        return
    }
    if(GetSite $siteName)
    {
        $app = New-Item IIS:\Sites\$siteName\$appName  -physicalPath $appPhysPath -type Application
        $site = Get-Item "IIS:\Sites\$siteName"
        Set-ItemProperty IIS:\Sites\$siteName\$appName -name applicationPool -value $site.applicationPool
        return $app
    }
 
}
 
 
function CheckBindingInfo([string]$ports)
{
    $portList=$ports.split(',')
    $bindA = @{}
    $bindB = @{}
    $portA = $portList[0]
    $portB = $portList[1]
    if($portList.Length -ne 2)
    {    
        Write-Host "格式错误"
        break
    }
    if(![string]::IsNullOrEmpty($portA.trim()))
    {
        $bindA=@{protocol="http";bindingInformation="*:"+$portA+":"}
    }
    
    if(![string]::IsNullOrEmpty($portB.trim()))
    {
        $bindB=@{protocol="net.tcp";bindingInformation=$portB+":"}
    }
    if(($bindA.Count -eq 0) -and !($bindB.Count -eq 0))
    {
        return $bindB
    }
    if(!($bindA.Count -eq 0) -and ($bindB.Count -eq 0))
    {
        return $bindA
    }
    if(!($bindA.Count -eq 0) -and !($bindB.Count -eq 0))
    {
        return $bindA,$bindB
    }    
    return $null
}
 
function CreateAppPool([string]$appPool,[string]$runtimeVersion="v4.0",[int]$pipelineMode=1)
{
    #待优化
    $apool = New-Item IIS:\AppPools\$appPool
    Set-ItemProperty IIS:\AppPools\$appPool managedRuntimeVersion $runtimeVersion
    #1:Classic or 0:Integrated
    Set-ItemProperty IIS:\AppPools\$appPool managedPipelineMode $pipelineMode
    return $apool
}
 
 
 
function GetSite([string]$siteName)
{
    try
    {
        $site = Get-Item "IIS:\Sites\$siteName" -ErrorAction Stop
        return $site
    }catch [System.SystemException]
    {
        #Write-Host -foregroundColor "Red" "获取站点 $siteName 信息失败"
        return $null
    }
}
 
function GetApplication([string]$siteName,[string]$appName)
{
    if(GetSite $siteName)
    {
    try
    {
        $app = Get-Item "IIS:\Sites\$siteName\$appName" -ErrorAction Stop
        return $app
    }catch [System.SystemException]
    {
        #Write-Host -foregroundColor "Red" "获取应用程序 $appName 失败"
        return $null
    }
    }
}
 
 
function Pause
{
    Write-Host "Press any key to continue ..."
    [Console]::ReadKey($true)|Out-Null
    Write-Host
}try
{
    Import-Module WebAdministration -ErrorAction Stop
}catch [System.SystemException]
{
    Write-Host -foregroundColor "Red" "请先安装IIS管理脚本和工具："
    Write-Host -foregroundColor "Red" "Win2008 *,角色-->添加角色--->功能工具下面的'IIS管理脚本和工具'"
    Write-Host -foregroundColor "Red" "Win7 在卸载程序中，点击'打开或关闭Windows功能'"
    break
}
 
function CreateWebSite([string]$siteName,[string]$physicalPath,[string]$ports)
{
    if(GetSite $siteName)
    {
        #todo: 待优化
        Write-Host "站点已经存在"
        return
    }
    $bindings = CheckBindingInfo $ports
    try
    {
        $site = New-Item IIS:\Sites\$siteName -bindings $bindings -physicalPath $physicalPath -ErrorAction Stop
        #todo: 待优化        
        $site.enabledProtocols = "http,net.tcp"
    }catch [System.SystemException]
    {
        Write-Host "创建站点失败"
        break
    }
    #todo: 待优化
    CreateAppPool $siteName
    Set-ItemProperty IIS:\Sites\$siteName -name applicationPool -value $siteName
    return $site
}
 
function CreateApplication([string]$siteName,[string]$appName,[string]$appPhysPath)
{
    #todo: 待优化
    if(GetApplication $siteName $appName)
    {
        Write-Host "应用程序已经存在"
        return
    }
    if(GetSite $siteName)
    {
        $app = New-Item IIS:\Sites\$siteName\$appName  -physicalPath $appPhysPath -type Application
        $site = Get-Item "IIS:\Sites\$siteName"
        Set-ItemProperty IIS:\Sites\$siteName\$appName -name applicationPool -value $site.applicationPool
        return $app
    }
 
}
 
 
function CheckBindingInfo([string]$ports)
{
    $portList=$ports.split(',')
    $bindA = @{}
    $bindB = @{}
    $portA = $portList[0]
    $portB = $portList[1]
    if($portList.Length -ne 2)
    {    
        Write-Host "格式错误"
        break
    }
    if(![string]::IsNullOrEmpty($portA.trim()))
    {
        $bindA=@{protocol="http";bindingInformation="*:"+$portA+":"}
    }
    
    if(![string]::IsNullOrEmpty($portB.trim()))
    {
        $bindB=@{protocol="net.tcp";bindingInformation=$portB+":"}
    }
    if(($bindA.Count -eq 0) -and !($bindB.Count -eq 0))
    {
        return $bindB
    }
    if(!($bindA.Count -eq 0) -and ($bindB.Count -eq 0))
    {
        return $bindA
    }
    if(!($bindA.Count -eq 0) -and !($bindB.Count -eq 0))
    {
        return $bindA,$bindB
    }    
    return $null
}
 
function CreateAppPool([string]$appPool,[string]$runtimeVersion="v4.0",[int]$pipelineMode=1)
{
    #待优化
    $apool = New-Item IIS:\AppPools\$appPool
    Set-ItemProperty IIS:\AppPools\$appPool managedRuntimeVersion $runtimeVersion
    #1:Classic or 0:Integrated
    Set-ItemProperty IIS:\AppPools\$appPool managedPipelineMode $pipelineMode
    return $apool
}
 
 
 
function GetSite([string]$siteName)
{
    try
    {
        $site = Get-Item "IIS:\Sites\$siteName" -ErrorAction Stop
        return $site
    }catch [System.SystemException]
    {
        #Write-Host -foregroundColor "Red" "获取站点 $siteName 信息失败"
        return $null
    }
}
 
function GetApplication([string]$siteName,[string]$appName)
{
    if(GetSite $siteName)
    {
    try
    {
        $app = Get-Item "IIS:\Sites\$siteName\$appName" -ErrorAction Stop
        return $app
    }catch [System.SystemException]
    {
        #Write-Host -foregroundColor "Red" "获取应用程序 $appName 失败"
        return $null
    }
    }
}
 
 
function Pause
{
    Write-Host "Press any key to continue ..."
    [Console]::ReadKey($true)|Out-Null
    Write-Host
}