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
## Controller
```php
myController::run();
myController::run('/base/url/path/');
myController::run('/', array('option' => 'value'));
```
```php
myController extends freestyle\controller{
    public function header(){
        echo '<div class="my-wrapper">';
    }
    public function footer(){
        echo '</div>';
    }
    public function action(){
        $value = $this->_action;
        $this->runController('valueController', array('value' => $value));
    }
    public function initSomeAction(){
        $app = $this->app(); // root controller
        $this->rel(); // construct url path relative to controller root (index) action
        $this->rel('relative/path');
        $this->arel(); // construct url path relative to current action
        $this->arel('relative/path');
        return $this->notFound(); // instead of response::notFound(), this forces exit from all controllers as there was no action method found
    }
    public function showName($name = 'default'){ // /name action
        // $name comes from $_POST or $_GET
        echo 'Hello, '.htmlspecialchars($name).'!';
    }
    public function init(){ // root(index) action
        response::redirect($this->rel('name'));
    }
    public function show(){ // root(index) action
    }
}
```
