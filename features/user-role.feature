Feature: User with ROLE_USER
  @createSchema
  Context:
    Given The user "user" is registered in DB with password "user"
    Given task "Task 1" is registered in DB with content "Lorem....."

  @roleUser
  Scenario: Logged user have can't access to users page
    Given I am on the login page
    When  I connect as "user" with the password "user"
    Then  I should be on homepage
    When  I go to "/users"
    Then  the response status code should be 403
    When  I go to "/users/create"
    Then  the response status code should be 403

  @createSchema @roleUser
  Scenario: logged user can edit task
    Given I am on the login page
    When  I connect as "user" with the password "user"
    Then  I should be on homepage
    When  I follow "tâches"
    And   I follow "task-edit-1"
    Then  I should be on "/tasks/1/edit"
    When  I fill in "task_title" with "title changed"
    And   I fill in "task_content" with "content changed"
    And   I press "Sauvegarder"
    Then  the response status code should be 200
    And   I should be on "/tasks"
    And   I should see "title changed"
    And   I should see "content changed"
    And   I should see "Superbe ! La tâche a bien été modifiée."

  @roleUser
  Scenario: logged user can change his account information
    Given I am on the login page
    When I connect as "user" with the password "user"
    Then I should be on homepage
    When I follow "Modifier mon profil"
    Then I should be on "/users/2/edit"
    But  I should not see "account_roles"
    When I fill in "account_username" with "changed-username"
    And  I fill in "account_email" with "changed@mail.de"
    And  I press "Sauvegarder"
    Then the response status code should be 200
    And  I should be on "/"
    And  I should see "changed-username" in the "#navbarDropdown" element
    When I follow "Modifier mon mot de passe"
    Then I should be on "/users/2/edit-password"
    When I fill in "update_password_actualPassword" with "test2"
    And  I fill in "update_password[newPassword]" with "*Password-1changed*"
    And  I fill in "update_password[confirmPassword]" with "*Password-1changed*"
    And  I press "Sauvegarder"
    Then the response status code should be 200
    When I follow "Se déconnecter"
    Then I should be on "/login"
    When I connect as "changed-username" with the password "*Password-1changed*"
    Then I should be on homepage