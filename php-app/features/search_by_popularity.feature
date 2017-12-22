@QP-3011
Feature: Ordering search by popularity
  As a Customer
  I want to get search ordered by popularity
  So that I find popular merchants quicker

  Scenario Outline: Merchant search ordered by popularity
    Given the following merchants exist:
      | Sportsshoes.com     |
      | ShopTo              |
      | Shoeaholics         |
      | Shoe Zone           |
      | Shoestore           |
      | SHOEPASSION.com     |
      | Shoetique           |
      | River Island        |
      | Office Shoes        |
      | Deckshoe Superstore |
      | Shoplet.co.uk       |
      | T.U.K Shoes         |
      | Shorefield Holidays |
    And I specify the sort order to be in "<direction>" order of "<field>"
    When I search for "<search term>"
    Then "<expected merchants>" should be in the specified order
    Examples:
      | search term | expected merchants                                                                                        | direction | field      |
      | bott        | Abbott Lyon, Boots, Bettersafe, Boots Kitchen Appliances, Boots Travel Insurance                          |           |            |
      | bott        | Abbott Lyon, Boots, Bettersafe, Boots Kitchen Appliances, Boots Travel Insurance                          | desc      | relevance  |
      | bott        | Boots, Boots Kitchen Appliances, Boots Travel Insurance, Boston Duvet and Pillow Co., Bettersafe          | desc      | popularity |

