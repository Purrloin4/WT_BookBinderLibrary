### 1. Unit Testing

   With unit testing we test the smallest testable units in our application. In most cases this would be a
   single class with its methods. In order to introduce a simple testable class in the application.


### 2. Integration Testing

   Integration testing will test a larger part of your application. Mostly a call to a method in one of the
   controllers that renders HTML as a result. Only the final result, being the HTML, for the integration
   of several components is tested (i.e. the ORM getting entities from the DB, the templating engine
   rendering HTML, …) 
   For us this means testing something like if the search bar is working correctly.


### 3. Functional testing

   The WebTestCase incorporated in the Symfony framework can only be used to assert static HTML
   code, i.e. it does not interpret and run client side (JavaScript) code. For doing in-browser testing a
   lot of frameworks can be used.
