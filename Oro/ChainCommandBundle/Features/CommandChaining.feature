Feature: Console Command Chaining
  In order to achieve desired functionality of application
  As a developer
  I want to have ability to link console commands in command chains

  Rules:
  - Other Symfony bundles in the application may register their console commands to be members of a command chain.
  - When a user runs the main command in a chain, all other commands registered in this chain should be executed as well.
  - Commands registered as chain members can no longer be executed on their own.
  - It must be implement detailed logging for chained commands execution.

  Scenario: Some Symfony bundle register in config file their console command to be member of a command chain
    Given there is some bundle with defined command "foo:hello"
    And command "foo:hello" registered in config file as member of chain for command "server:status" with priority = "15" and param "score" = "false"
    Then registered chain for command "server:status" should contains "foo:hello" command with priority = "15" and option "score" = "false"

  Scenario: Some Symfony bundle register at run-time their console command to be member of a command chain
    Given there is some bundle with defined command "bar:hi"
    And command "bar:hi" registered at run-time as member of chain for command "server:status" with priority = "15" and param "fname" = "Mike"
    Then registered chain for command "server:status" should contains "bar:hi" command with priority = "17" and option "fname" = "Mike"

  Scenario: User runs main command in a registered command chain
    Given there is some bundle with defined command "foo:hello"
    And command "foo:hello" registered in config file as member of chain for command "server:status" with priority = "15" and param "score" = "false"
    And there is some bundle with defined command "bar:hi"
    And command "bar:hi" registered at run-time as member of chain for command "server:status" with priority = "15" and param "fname" = "Mike"
    And Built-in php server is not running at the moment
    When I run console command "php bin/console server:status"
    Then I should get output "..."
    And an application log should contains "..."

  Scenario: User runs independently command registered as chain command member
    Given there is some bundle with defined command "foo:hello"
    And command "foo:hello" registered in config file as member of chain for command "server:status" with priority = "15" and param "score" = "false"
    When I run console command "php bin/console foo:hello --score=false"
    Then I should get output "..."
    And an application log should contains "..."

  Scenario: Command registered to be member of a command chain with missing required input options
    Given there is some bundle with defined command "foo:hello"
    And command "foo:hello" has required input option "score"
    And command "foo:hello" registered in config file as member of chain for command "server:status" with priority = "15" and param "verbose" = "3"
    And Built-in php server is not running at the moment
    When I run console command "php bin/console server:status"
    Then I should get output "..."
    And an application log should contains "..."
