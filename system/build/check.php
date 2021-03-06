<?php
/**
 * This file is used to check the language items and actions.
 */
/* Define an emtpty control class as the base class of every module. */
class control {}

/* set module root path and included the resource of group module. */
$moduleRoot = '../module/';
include $moduleRoot . '/group/lang/resource.php';
foreach(glob($moduleRoot . '/group/ext/lang/zh-cn/*.php') as $resourceFile)
{
    include $resourceFile;
}

$whiteList[] = 'user-login';

/* checking actions of every module. */
echo '-------------action checking-----------------' . "\n";
foreach(glob($moduleRoot . '*') as $modulePath)
{
    $moduleName  = basename($modulePath);
    if(strpos('install|upgrade|convert|common|misc|editor', $moduleName) !== false) continue;
    $controlFile = $modulePath . '/control.php';
    if(file_exists($controlFile))
    {
        include $controlFile;
        if(class_exists($moduleName))
        {
            $class   = new ReflectionClass($moduleName);
            $methods = $class->getMethods();
            foreach($methods as $method)
            {
                $methodRef = new ReflectionMethod($method->class, $method->name);
                if($methodRef->isPublic() and strpos($method->name, '__') === false)
                {
                    $methodName = $method->name;
                    if(in_array($moduleName . '-' . strtolower($method->name), $whiteList)) continue;
                    if(strpos($methodName, 'ajax') !== false) continue;

                    $exits = false;
                    foreach($lang->resource->$moduleName as $key => $label)
                    {
                        if(strtolower($methodName) == strtolower($key)) $exits = true;
                    }
                    if(!$exits) echo $moduleName . "\t" . $methodName . " not in the list. \n";
                }
            }
        }
    }
    $extControlFiles = glob($modulePath . '/ext/control/*.php');
    if($extControlFiles)
    {
        foreach($extControlFiles as $extControlFile)
        {
            $methodFile = substr($extControlFile, strrpos($extControlFile, '/') + 1);
            $methodName = substr($methodFile, 0, strpos($methodFile, '.'));
            if(in_array($moduleName . '-' . strtolower($methodName), $whiteList)) continue;
            if(strpos($methodName, 'ajax') !== false) continue;

            $exits = false;
            foreach($lang->resource->$moduleName as $key => $label)
            {
                if(strtolower($methodName) == strtolower($key)) $exits = true;
            }
            if(!$exits) echo $moduleName . "\t" . $methodName . " not in the list. \n";
        }
    }
}

/* checking actions of every module. */
echo '-------------lang checking-----------------' . "\n";
include '../../module/common/lang/zh-cn.php';
include '../../config/config.php';
foreach(glob($moduleRoot . '*') as $modulePath)
{
    unset($lang);
    $moduleName   = basename($modulePath);
    $mainLangFile = $modulePath . '/lang/zh-cn.php';
    if(!file_exists($mainLangFile)) continue;
    $mainLines = file($mainLangFile);

    foreach($config->langs as $langKey => $langName)
    {
        if($langKey == 'zh-cn' or $langKey == 'zh-tw') continue;
        $langFile = $modulePath . '/lang/' . $langKey . '.php';
        if(!file_exists($langFile)) continue;
        $lines = file($langFile);
        foreach($mainLines as $lineNO => $line)
        {
            if(strpos($line, '$lang') === false)
            {
                //if($line != $lines[$lineNO]) echo $moduleName . ' ' . $langKey . ' ' . $lineNO . "\n";
            }
            else
            {
                list($mainKey, $mainValue) = explode('=', $line);
                list($key, $value) = explode('=', $lines[$lineNO]);
                if(trim($mainKey) != trim($key))
                {
                    $key = trim($key);
                    $lineNO = $lineNO + 1;
                    echo "module $moduleName need checking, command is:";
                    echo " vim -O +$lineNO ../../module/$moduleName/lang/zh-cn.php +$lineNO ../../module/$moduleName/lang/en.php \n";
                    break;
                }
            }
        }
    }
}

echo '-------------php5.4 synatax checking-----------------' . "\n";
class app {function loadLang() {}}
$app = new app;
$lang = new stdclass();

error_reporting(E_WARNING | E_STRICT );
foreach(glob($moduleRoot . '*') as $modulePath)
{
    $moduleName = basename($modulePath);
    $cnLangFile = $modulePath . '/lang/zh-cn.php';
    $enLangFile = $modulePath . '/lang/en.php';
    $configFile = $modulePath . '/config.php';

    if(!isset($lang->$moduleName)) $lang->$moduleName = new stdclass();
    if(file_exists($cnLangFile)) include $cnLangFile;
    if(file_exists($enLangFile)) include $enLangFile;
    if(file_exists($configFile)) include $configFile;
}
