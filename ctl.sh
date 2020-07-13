#!/bin/bash


fun_install()
{
	composer install
}

fun_dump()
{
	composer dump-autoload
}


fun_help()
{
    echo "install 		安装依赖"
    echo "dump 			自定义类导出"

    exit 1
}



if [ $# -eq 0 ]
then
	fun_help
else
	fun_$1 $*
fi
exit 0
