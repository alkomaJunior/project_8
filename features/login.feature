Feature: Admin connect to App
  @createSchema
  Context:
    Given The users are registered in Database:
          username | password
            admin  |  admin
            user   |  user

  Scenario: User with 'ROLE_ADMIN' connect to app
    Given I am on the login page
    When I connect as "admin" with the password "admin"
    Then I should be on the homepage
    And  I should see "Se déconnecter"
    And  I should see "Créer un utilisateur"
    But  I should not see "Se connecter"

  Scenario: User with 'ROLE_USER' connect to app
    Given I am on the login page
    When I connect as "user" with the password "user"
    Then I should be on the homepage
    And  I should see "Se déconnecter"
    And  I should see "Créer une nouvelle tâche"
    But  I should not see "Créer un utilisateur"
    And  I should not see "Se connecter"
