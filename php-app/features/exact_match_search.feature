@QP-502 @QP-510 @QP-820
Feature: Exact match search
  As a customer searching on quidco,
  I want a merchant to be featured at the top of the search results when my search exactly matches the name of the merchant
  So that I can find the merchant I am searching for more easily and have more confidence in the search engine.

  Scenario Outline: The search term matches exactly
    Given the following merchants exist:
      | Hotels.com    |
      | ASOS          |
      | ao.com        |
      | Booking Buddy |
      | PetPlanet     |
      | George        |
    When I search for "<search query>"
    Then "<expected merchant>" should be the only merchant returned
    Examples:
      | search query  | expected merchant |
      | hotels.com    | Hotels.com        |
      | asos          | ASOS              |
      | ao.com        | ao.com            |
      | booking buddy | Booking Buddy     |
      | petplanet     | PetPlanet         |
      | george        | George            |
      | travelodgeuk  | Travelodge - UK   |

  Scenario Outline: The search term uses "and" word instead of the "&" symbol
    Given the following merchants exist:
      | Marks & Spencer |
      | B&Q             |
      | P&O Ferries     |
      | Cox & Cox       |
      | Mamas & Papas   |
      | Zee & Co        |
      | Toni & Guy      |
    When I search for "<search query>"
    Then "<expected merchant>" should be the only merchant returned
    Examples:
      | search query      | expected merchant |
      | red letter days   | Red Letter Days   |
      | argos credit card | Argos Credit Card |
      | p and o ferries   | P&O Ferries       |
      | cox and cox       | Cox & Cox         |
      | mamas and papas   | Mamas & Papas     |
      | zee and co        | Zee & Co          |
      | toni and guy      | Toni & Guy        |
