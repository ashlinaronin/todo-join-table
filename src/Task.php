<?php
class Task
{
    private $description;
    private $due_date;
    private $id;
    private $completed;

    function __construct($description, $due_date, $id = null, $completed = false)
    {
        $this->description = $description;
        $this->due_date = $due_date;
        $this->id = $id;
        $this->completed = (int) $completed;
    }

    function setDescription($new_description)
    {
        $this->description = (string) $new_description;
    }

    function setCompleted($new_completed)
    {
        $this->completed = (int) $new_completed;
    }

    function getDescription()
    {
        return $this->description;
    }

    function getId()
    {
        return $this->id;
    }

    function getDueDate()
    {
        return $this->due_date;
    }

    function setDueDate($new_due_date)
    {
        $this->due_date = $new_due_date;
    }

    function getCompleted()
    {
        return $this->completed;
    }

    function save()
    {
        $statement = $GLOBALS['DB']->exec("INSERT INTO tasks (description, due_date, completed) VALUES (
            '{$this->getDescription()}',
            '{$this->getDueDate()}',
            {$this->getCompleted()}
        );");
        $this->id = $GLOBALS['DB']->lastInsertId();

    }

    function addCategory($category)
    {
        $GLOBALS['DB']->exec("INSERT INTO categories_tasks (category_id, task_id) VALUES ({$category->getId()}, {$this->getId()});");
    }

    function getCategories()
    {
        $query = $GLOBALS['DB']->query("SELECT category_id FROM categories_tasks WHERE task_id = {$this->getId()};");
        $category_ids = $query->fetchAll(PDO::FETCH_ASSOC);

        $categories = array();
        foreach($category_ids as $id) {
            $category_id = $id['category_id'];
            $category_query = $GLOBALS['DB']->query("SELECT * FROM categories WHERE id = {$category_id};");
            $returned_category = $category_query->fetchAll(PDO::FETCH_ASSOC);

            $name = $returned_category[0]['name'];
            $id = $returned_category[0]['id'];
            $new_category = new Category($name, $id);
            array_push($categories, $new_category);
        }
        return $categories;
    }


    function updateCompleted($new_completed)
    {
        $GLOBALS['DB']->exec("UPDATE tasks SET completed = {$new_completed} WHERE id = {$this->getId()};");
        $this->setCompleted($new_completed);
    }

    static function getAll()
    {
        $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks ORDER BY due_date;");
        $tasks = array();
        foreach ($returned_tasks as $task) {
            $description = $task['description'];
            $due_date = $task['due_date'];
            $id = $task['id'];
            $completed = $task['completed'];
            $new_task = new Task($description, $due_date, $id, $completed);
            array_push($tasks, $new_task);
        }

        return $tasks;
    }

    static function deleteAll()
    {
        $GLOBALS['DB']->exec("DELETE FROM tasks;");
    }

    static function find($search_id)
    {
        $found_task = null;
        $tasks = Task::getAll();
        foreach ($tasks as $task) {
            $task_id = $task->getId();
            if ($task_id == $search_id) {
                $found_task = $task;
            }
        }
        return $found_task;
    }

    function update($new_description)
    {
        $GLOBALS['DB']->exec("UPDATE tasks SET description = '{$new_description}' WHERE id = {$this->getId()};");
        $this->setDescription($new_description);
    }

    function delete()
    {
        $GLOBALS['DB']->exec("DELETE FROM tasks WHERE id = {$this->getId()};");
        $GLOBALS['DB']->exec("DELETE FROM categories_tasks WHERE task_id = {$this->getId()};");
    }
}
?>
