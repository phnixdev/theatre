Feature: Index
  As a website user
  I am looking on the index pages

  Scenario: Looking at the homepage
    Given I am on the homepage
    Then I should see "No route found for \"GET /\""
    And the response status code should be 404
