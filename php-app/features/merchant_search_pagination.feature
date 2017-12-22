@QP-1616
Feature: Pagination of search results
  As a customer browsing on a merchant search results page
  I want to be able to view more pages of search results
  So that I can find more merchants related to my search term

  Scenario Outline:
    Given I select a page size of "<pageSize>"
    When I search for "<searchTerm>"
    And the total number of results are less than "<pageSize>"
    Then I want page "<page>" of the results to be returned
    And with no pagination available

    Examples:
      | searchTerm | pageSize | page |
      | dating     | 30       | 1    |
      | union      | 20       | 1    |


  Scenario Outline:
    Given I select a page size of "<pageSize>"
    And I select page "<page>"
    When I search for "<searchTerm>"
    And the total number of results are greater than "<pageSize>"
    Then I want page "<page>" of the results to be returned
    And I want a maximum of <pageSize> results to be shown on the page
    And with pagination available

    Examples:
      | searchTerm | pageSize | page |
      | home       | 10       | 1    |
      | car        | 15       | 2    |
      | game       | 4        | 2    |


  Scenario Outline:
    Given I select a page size of "<pageSize>"
    And "<pageSize>" is greater than the "<max size>"
    When I search for "<searchTerm>"
    And the total number of results are greater than "<max size>"
    Then I want a maximum of "<pageSize>" results to be shown on the page
    And with pagination available

    Examples:
      | searchTerm | pageSize | max size |
      | garden     | 12       | 10       |
      | travel     | 3        | 2        |