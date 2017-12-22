@QP-2858
Feature: In-store Merchant search filter
  As a Customer
  I want to search for a merchant available on the high street
  So that I find deals available in store

  Scenario Outline: Searching  merchants with in-store filter
    When I search for "<search term>" with the is-store filter set to "<filter>"
    Then All merchants found should have have the flag set to "<filter>"
    Examples:
      | search term     | filter |
      | power rangers   | yes    |
      | power rangers   | no     |
      | topcat          | no     |
      | pringles        | no     |
      | pringles        | yes    |
      | home            | yes    |
      | washing machine | yes    |
      | washing machine | no     |