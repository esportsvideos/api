Feature: Authentication
  As a visitor on the website
  I need to be able to login

  Scenario: Correct credentials
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/login_check" with body:
    """
    {
      "email":"user@esv.com",
      "password":"esv"
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "token" should exist

  Scenario: Incorrect password
    When I add "Content-Type" header equal to "application/json"
    And I send a "POST" request to "/login_check" with body:
    """
    {
      "email":"user@esv.com",
      "password":"bad_password"
    }
    """
    Then the response status code should be 401
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "code" should be equal to "401"
    And the JSON node "message" should be equal to "Bad credentials"

  Scenario: Inactive user
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/login_check" with body:
    """
    {
      "email":"inactive.user@esv.com",
      "password":"esv"
    }
    """
    Then the response status code should be 401
    And the header "Content-Type" should be equal to "application/json"
    And the response should be in JSON
    And the JSON node "code" should be equal to "401"
    And the JSON node "message" should be equal to "Bad credentials"
