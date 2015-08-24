<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Task.php";
    require_once __DIR__."/../src/Category.php";

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $app = new Silex\Application();

    $server = 'mysql:host=localhost:3306;dbname=to_do';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'
    ));

    $app['twig']->getExtension('core')->setTimezone('America/Los_Angeles');


    // Landing page. Allow user to create a new task or category.
    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig', array(
            'categories' => Category::getAll(),
            'tasks' => Task::getAll()
        ));
    });




    ////////    Tasks routes
    ////////////////////////////////
    // [R] Display all tasks, regardless of category. Allow user to add new tasks.
    $app->get("/tasks", function() use ($app) {
        return $app['twig']->render('tasks.html.twig', array(
            'tasks' => Task::getAll()
        ));
    });


    // [C] Create new task from form data, not yet associated with a category.
    // Then display all existing tasks.
    $app->post("/tasks", function() use ($app) {
        $description = $_POST['description'];
        $due_date = $_POST['due_date'];
        $task = new Task($description, $due_date);
        $task->save();
        return $app['twig']->render('tasks.html.twig', array(
            'tasks' => Task::getAll()
        ));
    });

    // [R] Display task with {id} and categories it belongs to. Allow user to assign this task
    // to a new category.
    $app->get("/tasks/{id}", function($id) use ($app) {
        $task = Task::find($id);
        return $app['twig']->render('task.html.twig', array(
            'task' => $task,
            'categories' => $task->getCategories(),
            'all_categories' => Category::getAll()
        ));
    });

    // [C] Add the current task to the category selected in the drop-down menu.
    // Then display all the categories currently associated with this task.
    $app->post("/add_categories", function() use ($app) {
        $category = Category::find($_POST['category_id']);
        $task = Task::find($_POST['task_id']);
        $task->addCategory($category);
        return $app['twig']->render('task.html.twig', array(
            'task' => $task,
            'tasks' => Task::getAll(),
            'categories' => $task->getCategories(),
            'all_categories' => Category::getAll()
        ));
    });







    ////////    Categories routes
    ////////////////////////////////

    // [R] Display all categories, regardless of task. Allow user to add new categories.
    $app->get("/categories", function() use ($app) {
        return $app['twig']->render('categories.html.twig', array(
            'categories' => Category::getAll()
        ));
    });

    // [C] Create new category from form data, not yet containing any tasks.
    // Then display all existing categories.
    $app->post("/categories", function() use ($app) {
        $category = new Category($_POST['name']);
        $category->save();
        return $app['twig']->render('categories.html.twig', array(
            'categories' => Category::getAll()
        ));
    });

    // [R] Display tasks assigned to category at {id}. Allow user to add a task to
    // this category or click a link to edit the category.
    $app->get("/categories/{id}", function($id) use ($app) {
        $category = Category::find($id);
        return $app['twig']->render('category.html.twig', array(
            'category' => $category,
            'tasks' => $category->getTasks(),
            'all_tasks' => Task::getAll()
        ));
    });

    // [C] Add the task selected in the drop-menu to the current category.
    // Then display all the tasks currently contained in this category.
    $app->post("/add_tasks", function() use ($app) {
        $category = Category::find($_POST['category_id']);
        $task = Task::find($_POST['task_id']);
        $category->addTask($task);
        return $app['twig']->render('category.html.twig', array(
            'category' => $category,
            'categories' => Category::getAll(),
            'tasks' =>$category->getTasks(),
            'all_tasks' => Task::getAll()
        ));
    });



    return $app;
?>
