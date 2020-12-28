Feature: Manage users
  @createSchema
  Context:
    Given The user "admin" is registered in DB with password "admin"

  @roleAdmin
  Scenario: Create new user from Admin
    Given I am on the login page
    When I connect as "admin" with the password "admin"
    Then I should be on homepage
    When I follow "Créer un utilisateur"
    Then I should be on "/users/create"
    When I create user with username: "behat", email: "behat@test.de",password: "Behat*-147" and role: "ROLE_ADMIN"
    Then the response status code should be 200
    And  I should be on "/users"
    And  I should see "behat" in the "td" element

  @createSchema @roleAdmin
  Scenario: Edit user from Admin
    Given I am on the login page
    When I connect as "admin" with the password "admin"
    Then I should be on homepage
    When I follow "Utilisateurs"
    Then I should be on "/users"
    When I follow "user-2"
    Then I should be on "/users/2/edit"
    When I fill in "account_username" with "changed-username"
    And  I fill in "account_email" with "changed@mail.de"
    And  I select "Administrateur" from "account_roles"
    And  I press "Sauvegarder"
    Then the response status code should be 200
    And  I should be on "/users"
    And  I should see "changed-username"
    And  I should see "changed@mail.de"
    But  I should not see "userx@todoandco.de"

  @createSchema @roleAdmin
  Scenario: Delete task from Admin
    Given I am on the login page
    When I connect as "admin" with the password "admin"
    Then I should be on homepage
    When I follow "Tâches"
    Then I should be on "/tasks"
    When I press "task-delete-1"
    Then the response status code should be 200
    And  I should be on "/tasks"
    And  I should see "Superbe ! La tâche a bien été supprimée."
    But  I should not see "Task 1"
