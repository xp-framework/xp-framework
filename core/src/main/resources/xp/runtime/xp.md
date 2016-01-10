# XP runtime

* Display version and classloader information
  ```sh
  $ xp -v
  ```
* Evaluate code
  ```sh
  $ xp -e 'var_dump(1 + 2)'
  ```
* Evaluate code and write returned result
  ```sh
  $ xp -w 'use util\Date; return Date::now()'
  ```
* Running classes from the class path:
  ```sh
  $ xp com.example.HelloWorld
  ```
* Running classes referenced by their file name:
  ```sh
  $ xp AgeInDays.class.php 1977-12-24
  ```
* Running a XAR archive's main class:
  ```sh
  $ xp find.xar src/main/php '*.xp'
  ```

The class path can be modified by adding one or more *-cp {path}*
before any other options. Modules can be loaded by using *-m {path}*.

