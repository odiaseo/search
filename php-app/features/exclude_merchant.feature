@QP-1098
Feature: Exclude Specific Merchants From Results
  As a customer not logged in searching on quidco,
  I should not see merchants only logged in customers in the control group should see

  Scenario Outline: Exclude Merchants
    Given the following merchants exist:
      | Asda                      |
      | Hotels.com                |
      | ASOS                      |
      | Tesco Wine By The Case    |
      | Booking Buddy             |
      | PetPlanet                 |
      | George                    |
    When I search for "<search query>" with "<merchants>" filtered
    Then the filtered "<merchants>" should not be returned
    Examples:
      | search query              | merchants                                                                     |
      | Asda                      | Asda,Hotels.com,Asos,Booking Buddy,PetPlanet,George,Direct Line Car Insurance |
      | Hotels.com                | Asda,Hotels.com,Asos,Booking Buddy,PetPlanet,George,Direct Line Car Insurance |
      | ASOS                      | Asda,Hotels.com,Asos,Booking Buddy,PetPlanet,George,Direct Line Car Insurance |
      | Tesco Wine By The Case    | Asda,Hotels.com,Asos,Booking Buddy,PetPlanet,George,Direct Line Car Insurance |
      | Booking Buddy             | Asda,Hotels.com,Asos,Booking Buddy,PetPlanet,George,Direct Line Car Insurance |
      | PetPlanet                 | Asda,Hotels.com,Asos,Booking Buddy,PetPlanet,George,Direct Line Car Insurance |
      | George                    | Asda,Hotels.com,Asos,Booking Buddy,PetPlanet,George,Direct Line Car Insurance |
