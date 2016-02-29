<?php
    require_once __DIR__ .'/../vendor/autoload.php';
    require_once __DIR__.'/../src/Task.php';
    require_once __DIR__.'/../src/Category.php';

    $app = new Silex\Application();

    $server = 'mysql:host=localhost;dbname=to_do';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'
    ));

    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig');
    });

    $app->get("/tasks", function() use ($app) {
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll(), 'categories' => Category::getAll()));
    });

    $app->post("/add_task", function() use ($app) {
        $description = $_POST['description'];
        $due_date = $_POST['due_date'];
        $id = null;
        $new_task = new Task($description, $id, $due_date);
        $new_task->save();
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll(), 'categories' => Category::getAll()));
    });

    $app->get("/task/{id}", function($id) use ($app) {
        $task = Task::find($id);
        $task_categories = $task->getCategories();
        return $app['twig']->render('task.html.twig', array('task_categories' => $task_categories, 'task' => $task, 'categories' => Category::getAll()));
    });

    $app->post("/task_add_category", function() use($app) {
        $task = Task::find($_POST['task_id']);
        $description = $task->getDescription();
        $due_date = $task->getDueDate();
        $categories = $task->getCategories();
        $task->save();
        return $app['twig']->render('task.html.twig', array('task' => $task, 'categories' => $categories));
    });

    $app->patch("/update_task/{id}", function($id) use ($app) {
        $task = Task::find($id);
        $new_description = $_POST['new_description'];
        $new_due_date = $_POST['new_due_date'];
        $task->update($new_description, $new_due_date);
        $categories = $task->getCategories();
        return $app['twig']->render('task.html.twig', array('description' => $new_description, 'due_date' => $new_due_date, 'categories' => $categories));
    });

    $app->delete("/delete_task/{id}", function($id) use ($app) {
        $task = Task::find($id);
        $task->delete();
        return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
    });





    $app->get("/categories", function() use ($app) {
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    $app->post("/add_category", function() use ($app) {
        $category = new Category($_POST['name']);
        $category->save();
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    $app->get("/category/{id}", function($id) use ($app) {
      $category = Category::find($id);
      $tasks = Task::getAll();
      return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks(), 'tasks_all' => $tasks));
    });


    $app->post("/delete_categories", function() use ($app) {
        Category::deleteAll();
        return $app['twig']->render('index.html.twig');
    });

    $app->post("/delete_tasks", function() use ($app) {
        Task::deleteAll();
        return $app['twig']->render('index.html.twig');
    });

    $app->post("/tasks", function() use ($app) {
      $description = $_POST['description'];
      $category_id = $_POST['category_id'];
      $due_date = $_POST['due_date'];
      $task = new Task($description, $id = null, $category_id, $due_date);
      $task->save();
      $tasks = Task::getAll();
      $category = Category::find($category_id);
      return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks(), 'tasks_all' => $tasks));
    });

    return $app;
?>
