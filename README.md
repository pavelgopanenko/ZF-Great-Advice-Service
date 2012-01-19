Fucking Great Advice
========================

Клиент для API fucking-great-advice.ru, сайта который даёт охуенные советы всем нуждающимся.

###Требования

ZendFramework 1.11.11 (на других версия не проверялось, но должно работать)

###Использование
<code php>
$fga = new Ulrika_Service_FuckingGreatAdvice();
$advice = $fga->getRandom();
echo $advice['text'];
</code>
