# Third-Party-Payment-Exercise 第三方支付的小小練習      
    
**重點筆記筆記**
==================================
    
    
### 使用php MySQL pdo method獲取資料庫會員資料   
`php pdo`用了許多箭頭與法，會比較抽象，但是理解之後就方便許多   
不用再`$query = mysqli_query`寫一堆了   

>請參閱：[PHP Connect to MySQL-w3schools](https://www.w3schools.com/php/php_mysql_connect.asp)  

### 使用php curl 可以 post xml 資料到第三方地址 
因為這個練習的第三方必須使用xml格式將data傳出去，所以找到了一個用法`cURL`   
可以省去跨網域的問題`Access-Control-Allow-Origin`    

>請參閱：[cURL Functions-PHP Manual](http://php.net/manual/en/function.curl-setopt.php)   

### 使用simplexml_load_string將回傳xml格式轉換   
`simplexml`是個滿方便的用法，可以轉變xml格式   
加入`LIBXML_NOCDATA`參數，可以去掉回傳的CDATA    
原本是使用`preg_match_all`的正規表達式抓值    
但是回傳的陣列順序不一樣就會抓錯   
改用`simplexml`可以用key抓值`$array["code_url"]`就不會亂抓data    

>請參閱：[PHP simplexml_load_string() Function-w3schools](https://www.w3schools.com/php/func_simplexml_load_string.asp)   
