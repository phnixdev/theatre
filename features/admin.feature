Feature: Admin
  As a user
  I am looking on the admin pages

  Scenario: Looking at the dashboard
    Given I am on "/admin/dashboard"
    Then I should see "Theatre Admin"
    And the "q" field should contain ""
    And I should see "Symfony Theatre"
    And the response status code should be 200
