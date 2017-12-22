@QP-900
Feature: Rates Text Phrase Exact Match
  As a customer searching on quidco,
  I want a merchant to be featured at the top of the search results
  When my search does not match the merchan's name exactly but matches a phrase contained in the rates text
  So that I can find the merchant I am searching with more familiar rate phrases

  @QP-900b
  Scenario Outline: The search term matches a phrase in rates text
    Given the following merchants exist:
      | Microsoft Store     |
      | Carphone Warehouse  |
      | NOW TV              |
      | Expedia             |
      | esure Car Insurance |
      | LEGOLAND® Holidays  |
    And the following terms are not merchant exact matches:
      | xbox                      |
      | CLG G5                    |
      | now tv entertainment pass |
      | electricals               |
      | package holidays          |
      | lingerie                  |
      | car insurance policy      |
    When I search for "<search query>" as rates text
    Then "<expected merchant>" should be returned
    Examples:
      | search query              | expected merchant   |
      | xbox                      | Microsoft Store     |
      | LG G5                     | Carphone Warehouse  |
      | now tv entertainment pass | NOW TV              |
      | electricals               | Zavvi               |
      | package holidays          | LEGOLAND® Holidays  |
      | car insurance policy      | esure Car Insurance |

  @QP-900c
  Scenario Outline: The search term  with stop words does not matches a phrase in merchants rates text
    Given the following merchants exist:
      | Zavvi                |
      | Expedia              |
      | Esure car insurance  |
      | Debenhams (in-store) |
    And the merchant "<merchant>" rates text description contains "<rates description>"
    When I search for "<search query>" as rates text
    Then the following "<ignored merchants>" should not be returned

    Examples:
      | merchant            | rates description                              | search query                   | ignored merchants                 |
      | Zavvi               | for electricals, consoles and technology sales | rates for electricals          | Expedia,Esure car insurance       |
      | Expedia             | for package holidays                           | rates for package holidays     | Zavvi,Esure car insurance         |
      | Esure car insurance | for new fully completed car insurance policy   | rates for lingerie             | Zavvi,Expedia,Esure car insurance |