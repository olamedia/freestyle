# Freestyle
Freestyle framework

# !DRAFT!

## Request, response
```php
if (request::isPost()){
    // process form
    response::redirect('/url/path');
}
response::on(404, function(){
    // custom handler, bind controller action to customize output message
});
if ($condition){
    response::notFound();
}
```



