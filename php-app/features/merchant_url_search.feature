Feature: Merchant search by URL
  As a customer who is searching on a merchant’s website
  I want the search toolbar to notify me when cashback is available for the merchant
  So that I can easily claim cashback without having to check if it's available on Shoop

  @QP-957
  Scenario Outline: Cashback is available
    Given I have the Shoop toolbar enabled in my web browser
    And the "<merchant>" merchant is on Shoop with cashback available for the following url rules: "<rules>"
    When I navigate to "<URL>"
    Then I should be notified about cashback available for this site
    Examples:
      | merchant | URL                                                      | rules                                                        |
      | Nike     | http://www.nike.com/fr                                   | www.nike.com/fr, store.nike.com/fr, secure-store.nike.com/fr |
      | Nike     | http://www.nike.com/fr/                                  | www.nike.com/fr, store.nike.com/fr, secure-store.nike.com/fr |
      | Nike     | http://www.nike.com/fr/fr_FR/c/boys/                     | www.nike.com/fr, store.nike.com/fr, secure-store.nike.com/fr |
      | Nike     | http://store.nike.com/fr/fr_fr/pw/gar%C3%A7on-chaussures | www.nike.com/fr, store.nike.com/fr, secure-store.nike.com/fr |
      | Topman   | http://fr.topman.com/                                    | eu.topman.com,fr.topman.com                                  |
      | Topman   | http://fr.topman.com/fr/tmfr/jeans-homme-2820408         | eu.topman.com,fr.topman.com                                  |
      | Topman   | http://eu.topman.com/en/tmeu/mens-jeans-617806           | eu.topman.com,fr.topman.com                                  |

  @QP-957
  Scenario Outline: Cashback is not available for the visited site
    Given I have the Shoop toolbar enabled in my web browser
    And the "<merchant>" merchant is on Shoop with cashback available for the following url rules: "<rules>"
    When I navigate to "<URL>"
    Then I should see that cashback is not available for the site
    Examples:
      | merchant | URL                                             | rules                                                        |
      | Nike     | http://www.nike.com/                            | www.nike.com/fr, store.nike.com/fr, secure-store.nike.com/fr |
      | Nike     | http://www.nike.com/gb                          | www.nike.com/fr, store.nike.com/fr, secure-store.nike.com/fr |
      | Topman   | http://www.topman.com/en/tmuk/mens-jeans-140508 | eu.topman.com,fr.topman.com                                  |
