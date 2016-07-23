varhound
========

Statically collects types of local class/method variables and types of method calls results based on PHP grammar parser


Goal
--------------------------
Use collected information to create the index of Types usages.

Index allows:
- find all usages of specific Type
- suggest types annotations for the properties and methods.
- find annotations with wrong or not specific types.

Implemented functionality:
---------------------------

- Based on annotations and type hinting assign types to the variables in method scope
- Based on result of method invocation assign type to the variables which stores result
- Based on type of variable in method scope, retrieved by any of the previous ways, assign type to the method return statement
- Retrieve the type of method invocation chaining

Limitations:
--------------------------
Indexer hardly relies on annotations. Its results will be better for the clusters where the number of annotations is higher,
it improves results for the parts of code which uses well-annotated code, but it will be poor for the parts which uses unannotated code.


TODO:
---------------------------

General:
- update reflection index with suggested types information after each method passed. This will improve suggestion process. Make it independent of processing order.
- collect types of variables passed to the method
- support use statement
- support arrays, make suggestions for array element types
- make resolution in case of conflicts between annotations, type hinting and suggestions
- process loops, conditional statements and other sub-scopes. Support local to scope vars lifetime.

Magento Specific:
- find Magento "magic methods" and assign types to them
- assign types to the object manager call
- assign type to the registry calls

Tools:
- create web interface for search
- create tool for annotations suggestions based on index

Usage:
---------------------------
//to run the tests
php run.php
