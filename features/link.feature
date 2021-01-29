Feature: Create & edit user
  @createSchema
  Context:
    Given user "admin" with id=1 is registered in DB with password "admin"
    Given user "user" with id=2 is registered in DB with password "user"
    Given task "Task 1" is registered in DB

  @links
  Scenario: Try all links in the app
    Given I am on the login page
    When I connect as "admin" with the password "admin"
    Then I should be on "/"
    When I follow "Modifier mon profil"
    Then I should be on "/users/1/edit"
    When move backward one page
    Then I should be on "/"
    When I follow "Modifier mon mot de passe"
    Then I should be on "/users/1/edit-password"
    When I follow "Utilisateurs"
    Then I should be on "/users"
    When I follow "Créer un utilisateur"
    Then I should be on "/users/create"
    When I follow "Retour à la liste des utilisateurs"
    Then I should be on "/users"
    When I follow "user-2"
    Then I should be on "/users/2/edit"
    When I follow "Modifier mot de passe"
    Then I should be on "/users/2/edit-password"
    When I follow "Retour à la liste des utilisateurs"
    Then I should be on "/users"
    When I follow "Tâches"
    When I follow "Tâches términées"
    Then I should be on "/tasks/done/true"
    When I follow "Tâches"
    When I follow "Tâches non términées"
    Then I should be on "/tasks/done/false"
    When I follow "Tâches"
    When I follow "Toutes les tâches"
    Then I should be on "/tasks"
    When I follow "task-edit-1"
    Then I should be on "/tasks/1/edit"
    When I follow "Retour à la liste des tâches"
    Then I should be on "/tasks"
    When I follow "Créer une nouvelle tâche"
    Then I should be on "/tasks/create"
    When I follow "OpenClassrooms"
    Then I should be on "/"
    When I follow "Créer un utilisateur"
    Then I should be on "/users/create"
    When move backward one page
    Then I should be on "/"
    When I follow "Créer une nouvelle tâche"
    Then I should be on "/tasks/create"
    When move backward one page
    Then I should be on "/"
    When I follow "Consulter la liste des tâches à faire"
    Then I should be on "/tasks/done/false"
    When move backward one page
    Then I should be on "/"
    When I follow "Consulter la liste des tâches terminées"
    Then I should be on "/tasks/done/true"
    When move backward one page
    Then I should be on "/"
    When I follow "Se déconnecter"
    Then I should be on "/"