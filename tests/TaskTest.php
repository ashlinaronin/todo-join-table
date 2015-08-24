<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Task.php";
    require_once "src/Category.php";

    $server = 'mysql:host=localhost:3306;dbname=to_do_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);


    class TaskTest extends PHPUnit_Framework_TestCase
    {

        protected function tearDown()
        {
            Task::deleteAll();
            Category::deleteAll();
        }

        function test_getDescription()
        {
            //Arrange
            $description = "Do dishes.";
            $due_date = "1234-12-12";
            $task = new Task($description, $due_date);

            //Act
            $result = $task->getDescription();

            //Assert
            $this->assertEquals($description, $result);
        }

        function test_setDescription()
        {
            //Arrange
            $description = "Do dishes.";
            $due_date = "1234-12-12";
            $task = new Task($description, $due_date);

            //Act
            $task->setDescription("Drink coffee.");
            $result = $task->getDescription();

            //Assert
            $this->assertEquals("Drink coffee.", $result);
        }

        function test_getId()
        {
            //Arrange
            $description = "Wash the dog";
            $due_date = "2015-09-16";
            $task = new Task($description, $due_date);
            $task->save();

            //Act
            $result = $task->getId();

            //Assert
            $this->assertEquals(true, is_numeric($result));
        }

        function test_save()
        {
            //Arrange
            $description = "Wash the dog";
            $due_date = "2015-09-16";
            $id = 1;
            $task = new Task($description, $due_date, $id);

            //Act
            $task->save();

            //Assert
            $result = Task::getAll();
            $this->assertEquals($task, $result[0]);

        }

        function test_saveSetsId()
        {
            //Arrange
            $description = "Wash the dog";
            $id = 1;
            $task = new Task($description, $id);

            //Act
            $task->save();

            //Assert
            $this->assertEquals(true, is_numeric($task->getId()));
        }

        function test_getAll()
        {
            //Arrange
            $description = "Wash the dog";
            $due_date = "2015-09-16";
            $id = 1;
            $task = new Task($description, $due_date, $id);
            $task->save();

            $description2 = "water the lawn";
            $due_date2 = "2015-09-16";
            $id2 = 2;
            $task2 = new Task($description2, $due_date2, $id2);
            $task2->save();

            //Act
            $result = Task::getAll();

            //Assert
            $this->assertEquals([$task, $task2], $result);
        }

        function test_deleteAll()
        {
            //Arrange
            $description = "Wash the dog";
            $due_date = "2015-09-16";
            $id = 1;
            $task = new Task($description, $due_date, $id);
            $task->save();

            $description2 = "Water the lawn";
            $due_date2 = "2015-09-16";
            $id2 = 2;
            $task2 = new Task($description2, $due_date2, $id2);
            $task2->save();

            //Act
            Task::deleteAll();

            //Assert
            $result = Task::getAll();
            $this->assertEquals([], $result);
        }



        function test_find()
        {
            //Arrange
            $description = "Wash the dog";
            $due_date = "2015-09-16";
            $id = 1;
            $task = new Task($description, $due_date, $id);
            $task->save();

            $description2 = "Water the lawn";
            $due_date2 = "3015-09-16";
            $id2 = 2;
            $task2 = new Task($description2, $due_date2, $id2);
            $task2->save();

            //Act
            $result = Task::find($task->getId());

            //Assert
            $this->assertEquals($task, $result);
        }

        function test_update()
        {
            //Arrange
            $description = "Wash the dog";
            $due_date = "1234-12-12";
            $id = 1;
            $task = new Task($description, $due_date, $id);
            $task->save();

            $new_description = "Clean the dog";

            //Act
            $task->update($new_description);

            //Assert
            $this->assertEquals("Clean the dog", $task->getDescription());
        }

        function test_deleteTask()
        {
            //Arrange
            $description = "Wash the dog";
            $due_date = "1234-12-12";
            $id = 1;
            $task = new Task($description, $due_date, $id);
            $task->save();

            $description2 = "Water the lawn";
            $due_date2 = "1234-12-12";
            $id2 = 2;
            $task2 = new Task($description2, $due_date2, $id2);
            $task2->save();

            //Act
            $task->delete();

            //Assert
            $this->assertEquals([$task2], Task::getAll());
        }

        function test_addCategory()
        {
            //Arrange
            $name = "Christmas Things";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();

            $description = "Decorate the tree";
            $id2 = 2;
            $test_task = new Task($description, $id2);
            $test_task->save();

            //Act
            $test_task->addCategory($test_category);

            //Assert
            $this->assertEquals($test_task->getCategories(), [$test_category]);
        }

        function test_getCategories()
        {
            //Arrange
            $name = "Halloween Stuff";
            $id = 1;
            $test_category = new Category($name, $id);
            $test_category->save();

            $name2 = "Carve the pumpkin";
            $id2 = 2;
            $test_category2 = new Category($name2, $id2);
            $test_category2->save();

            $description = "Scare trick or treaters";
            $id3 = 3;
            $test_task = new Task($description, $id3);
            $test_task->save();

            //Act
            $test_task->addCategory($test_category);
            $test_task->addCategory($test_category2);

            //Assert
            $this->assertEquals($test_task->getCategories(), [$test_category, $test_category2]);
        }

        function test_delete()
        {
            //Arrange
           $name = "Work stuff";
           $test_category = new Category($name);
           $test_category->save();

           $description = "File reports";
           $due_date = "2014-09-08";
           $test_task = new Task($description, $due_date);
           $test_task->save();

           //Act
           $test_task->addCategory($test_category);
           $test_task->delete();

           //Assert
           $this->assertEquals([], $test_category->getTasks());
        }

        function test_getCompleted()
        {
            //Arrange
            $description = "Walk the dog";
            $due_date = "4000-08-08";
            $id = null;
            $completed = false;
            $test_task = new Task($description, $due_date, $id, $completed);
            $test_task->save();

            //Act
            $result = Task::getAll();

            //Assert
            $this->assertEquals(false, $result[0]->getCompleted());
        }

        function test_updateCompleted()
        {
            //Arrange
            $description = "Walk the dog";
            $due_date = "4000-08-08";
            $id = null;
            $completed = false;
            $test_task = new Task($description, $due_date, $id, $completed);
            $test_task->save();

            //Act
            $test_task->updateCompleted(true);
            $result = Task::getAll();

            //Assert
            $this->assertEquals(true, $result[0]->getCompleted());
        }
    }


?>
