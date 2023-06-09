Batch Processing with a Custom Drush Command in Drupal 9

In this post we are going to create a custom drush command that loads the nodes of a content type passed in as an argument ($type). then a batch process will simulate a long operation on each node.

Create a batchManager class for the batch operations and services (batchManager.php):
In this class we define callbacks functions:
processItem(): In the processItem method we are going to process each element of our batch. As you can see, in this method we just simulate a long operation with the sleep() PHP function. here we could load each node or connect with an external API. We also grab some information for post-processing.
processFinished(): In this function we display relevant information to the user, and we can even save the unprocessed operations for a later process.
Create the custom Drush command to launch the batch:
Drush command is composed of two files:
 examples_batch.services.yml: This is a Symfony service definition, where our Drush command definition goes into, you'll can see that in our example we inject two core services in our command class: entity_type.manager to access the nodes to process and logger.factory to log some pre-process and post-process information.
batchCommands.php: In this class we are going to define the custom Drush commands of our module. This class uses the Annotated method for commands. 

The first we inject our services in the __construct() method: 
entity_type.manager
logger.factory
examples_batch.batch.manager

Next, in the watcher() annotated method we define our command with annotations @command and @aliases, the main part of this command is the creation of the operations array for our batch processing also pointing to the two callback functions, Once the batch operations are added as new batch sets, we process the batch sets with the function drush_backend_batch_process().

Finally, we show information to the user and log some information for a later use.

Some of the annotations available for use are:
@command: This annotation is used to define the Drush command.
@aliases: An alias for your command.
@param: Defines the input parameters. 
@option: Defines the options available for the commands.

