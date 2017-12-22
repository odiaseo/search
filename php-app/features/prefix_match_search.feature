@QP-1329
Feature: Prefix match search
  As a customer searching on quidco,
  I want a merchant to be featured at the top of the search results when my search matches the beginning of a merchant's name
  So that I can find the merchant I am searching starting with a search term.

  Scenario Outline: The search term matches merchant name prefix
    Given the following merchants exist:
      | Toys R Us and Babies R Us |
      | ASOS                      |
      | Booking Buddy             |
      | PetPlanet                 |
      | George                    |
      | 123-reg.co.uk             |
      | My-picture.co.uk          |
      | DatingDirect              |
    When I search for "<search query>"
    Then "<expected merchant>" should be the only merchant returned
    And  "<expected merchant>" should be part of the returned name prefix matches
    Examples:
      | search query | expected merchant         |
      | aso          | ASOS                      |
      | toys R U     | Toys R Us and Babies R Us |
      | booking bud  | Booking Buddy             |
      | petplane     | PetPlanet                 |
      | geor         | George                    |
      | 123reg       | 123-reg.co.uk             |
      | mypi         | My-picture.co.uk          |
      | datingd      | DatingDirect              |


  Background:
    Given the following merchants exist:
      | 123-reg.co.uk                  |
      | 123 Ink Cartridges             |
      | 123spareparts.co.uk            |
      | Travelodge - UK                |
      | My-picture.co.uk               |
      | AA UK Breakdown Cover          |
      | AA Home Insurance              |
      | AA Credit Cards                |
      | Quidco Compare Home Insurance  |
      | AA Motorcycle Insurance        |
      | Hotels.com                     |
      | Hobbycraft                     |
      | House of Fraser                |
      | Holiday Extras Airport Parking |
      | Appliances Direct              |
      | APH Airport Parking and Hotels |
      | Asda                           |
      | ASOS                           |

  @QP-507
  Scenario Outline: Searching with beginning of a word
    When I search for "<search term>"
    Then "<expected merchants>" should be part of the returned name prefix matches
    Examples:
      | search term | expected merchants                                                                |
      | 123         | 123-reg.co.uk, 123 Ink Cartridges, 123spareparts.co.uk                            |
      | aa          | AA UK Breakdown Cover, AA Home Insurance, AA Credit Cards,AA Motorcycle Insurance |
      | as          | Asda, ASOS                                                                        |


  @QP-2938
  Scenario Outline: Merchant name prefix match
    Given the following merchants exist:
      | BTR Direct            |
      | BT Broadband          |
      | BT Shop               |
      | BT Business Direct    |
      | BT Mobile             |
      | BT Business Broadband |
    When I search for "<search term>"
    Then "<expected merchant>" should be one of the returned name prefix matches
    And  "<expected merchant>" should be in the specified order
    Examples:
      | search term | expected merchant                                                                       |
      | bt          | BT Broadband, BT Mobile, BT Shop, BT Business Broadband, BT Business Direct, BTR Direct |