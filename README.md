## Содержимое репозитория

В репозитории находятся свежая версия библиотеки TCPDF (http://www.tcpdf.org/) и тестовый файл.

Тестовый скрипт состоит из:

* подключения библиотеки;
* функции генерации простейшего PDF файла, зашифрованного 256 битным ключом по алгоритму AES;
* циклического вызова функции с замером количества сгенерированных файлов за 1 секунду.

## Установка

Потребуется установить libmcrypt-devel и собрать PHP с mcrypt.

mcrypt можно установить из EPEL (решение взято [отсюда](http://www.omniweb.com/wordpress/?p=640), обновлена версия EPEL):

```Shell
# rpm -ivh http://download.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm
# yum install libmcrypt-devel
```

После этого можно пересобрать PHP с `--with-mcrypt` и собрать расширение (решение из [комментария на php.net](http://php.net/manual/en/mcrypt.installation.php)):

```Shell
# cd php-5.x.x/ext/mcrypt
# phpize
# aclocal
# ./configure
# make && make install
```

Расширение нужно включить, добавив в `php.ini` строку `extension=mcrypt.so`.

## Использование

Библиотеку TCPDF можно хранить в репозитории (как сделано здесь), либо установить через `composer`:

```JSON
{
    "require": {
        "tecnick.com/tcpdf": "dev-master"
    }
}
```

Минимальный код для генерации PDF файла с паролем `testpassword`:

```PHP
require_once 'tcpdf_include.php';

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$html = "Hello world!";

$pdf->AddPage();
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
$pdf->SetProtection([], 'testpassword', null, 3);
$pdf->Output("example.pdf", 'F');    
```

Здесь будет сгенерирован файл `example.pdf`, пароль установлен с помощью вызова `TCPDF::SetProtection()`.

## Тестирование и производительность

В среднем за 1 секунду на тестовом стенде скрипт генерировал 92 файла.

Производительность тестировалась на виртуальной машине VirtualBox, которой было выделено одно ядро. Диск виртуальной машины сохранен на SSD.  
В виртуальной машине были установлены CentOS 6.3 с PHP 5.4.20, собранным из исходников и дополненным библиотекой mcrypt.

```Shell
# cat /proc/cpuinfo

processor	: 0
vendor_id	: GenuineIntel
cpu family	: 6
model		: 42
model name	: Intel(R) Core(TM) i5-2467M CPU @ 1.60GHz
stepping	: 7
cpu MHz		: 1594.996
cache size	: 6144 KB
fpu		: yes
fpu_exception	: yes
cpuid level	: 5
wp		: yes
flags		: fpu vme de pse tsc msr pae mce cx8 apic mtrr pge mca cmov pat pse36 clflush mmx fxsr sse sse2 syscall nx rdtscp lm constant_tsc up rep_good pni monitor ssse3 lahf_lm
bogomips	: 3189.99
clflush size	: 64
cache_alignment	: 64
address sizes	: 36 bits physical, 48 bits virtual
power management:
```

Тестовый скрипт выполняет генерацию зашифрованных PDF файлов до тех пор, пока время генерации не превысит 1 секунду. Скрипт выводит количество файлов, сгенерированных за секунду.

```Shell
# SUM=0; N=100; for ((i=1; i<=$N; i++)); do R=`php test.php`; echo -n $R" "; SUM=`expr $SUM + $R`; rm -rf files/*; done; echo ''; echo '----'; echo 'AVG: '`expr $SUM / $N`
92 93 89 90 92 92 92 90 93 95 96 89 90 90 93 95 90 95 94 94 87 92 93 94 90 93 94 96 93 92 92 92 91 94 94 95 96 92 93 71 93 93 93 91 94 93 94 91 94 95 95 95 86 93 95 89 92 92 91 92 92 90 91 93 94 94 92 91 92 95 93 95 93 93 94 92 94 94 94 91 93 94 93 88 89 92 91 94 92 90 94 94 92 90 88 94 92 92 94 93
----
AVG: 92
```
