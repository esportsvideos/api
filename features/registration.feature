Feature: Registration
  As a new visitor on the website
  I need to be able to create an account

  Scenario: Complete registration
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/users" with body:
    """
    {
      "username":"john_doe",
      "email":"john.doe@esv.com",
      "plainPassword":"1234PASSword!!"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "@id" should be equal to "/users/john-doe"
    And the JSON node "@type" should be equal to "User"

  Scenario: Registration failed - Email already used
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/users" with body:
    """
    {
      "username":"username_good",
      "email":"user@esv.com",
      "plainPassword":"1234PASSword!!"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "violations[0].propertyPath" should be equal to 'email'
    And the JSON node "violations[0].message" should be equal to 'This value is already used.'

  Scenario: Registration failed - Username already used
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/users" with body:
    """
    {
      "username":"user",
      "email":"good@email.com",
      "plainPassword":"1234PASSword!!"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "violations[0].propertyPath" should be equal to 'username'
    And the JSON node "violations[0].message" should be equal to 'This value is already used.'

  Scenario: Registration failed - Password incorrect - 12 chars
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/users" with body:
    """
    {
      "username":"username_good",
      "email":"good@email.com",
      "plainPassword":"1234PASSw!!"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "violations[0].propertyPath" should be equal to 'plainPassword'
    And the JSON node "violations[0].message" should be equal to 'This value is too short. It should have 12 characters or more.'

  Scenario: Registration failed - Password incorrect - 1 uppercase
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/users" with body:
    """
    {
      "username":"username_good",
      "email":"good@email.com",
      "plainPassword":"1234password!!"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "violations[0].propertyPath" should be equal to 'plainPassword'
    And the JSON node "violations[0].message" should be equal to 'Your password must have at least 1 uppercase, 1 lowercase, 1 number and 1 special character.'

  Scenario: Registration failed - Password incorrect - 1 lowercase
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/users" with body:
    """
    {
      "username":"username_good",
      "email":"good@email.com",
      "plainPassword":"1234PASSWORD!!"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "violations[0].propertyPath" should be equal to 'plainPassword'
    And the JSON node "violations[0].message" should be equal to 'Your password must have at least 1 uppercase, 1 lowercase, 1 number and 1 special character.'

  Scenario: Registration failed - Password incorrect - 1 number
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/users" with body:
    """
    {
      "username":"username_good",
      "email":"good@email.com",
      "plainPassword":"THISisApassword!!"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "violations[0].propertyPath" should be equal to 'plainPassword'
    And the JSON node "violations[0].message" should be equal to 'Your password must have at least 1 uppercase, 1 lowercase, 1 number and 1 special character.'

  Scenario: Registration failed - Password incorrect - 1 special char
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/users" with body:
    """
    {
      "username":"username_good",
      "email":"good@email.com",
      "plainPassword":"1234PASSwordfail"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/ld+json; charset=utf-8"
    And the JSON node "violations[0].propertyPath" should be equal to 'plainPassword'
    And the JSON node "violations[0].message" should be equal to 'Your password must have at least 1 uppercase, 1 lowercase, 1 number and 1 special character.'
