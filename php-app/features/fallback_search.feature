Feature: Fallback search
  As a Customer
  I want to search for a merchant even if I don't know their exact name
  So that I find cashback opportunities quicker

  Background:
    Given the following merchants exist:
      | 123-reg.co.uk             |
      | Travelodge - UK           |
      | My-picture.co.uk          |
      | The Range                 |
      | Multipower UK             |
      | UK Power - Compare Energy |
      | Topman                    |
      | Topshop                   |
      | Toolstop                  |
      | Brastop                   |
      | Coggles                   |
      | The Single Solution       |
      | Coast                     |
      | Oasis                     |
      | Booking.com               |
      | Littlewoods               |
      | Marks & Spencer           |
      | Argos                     |

  @QP-512
  Scenario Outline: Searching with keywords matching anywhere in the name
    When I search for "<search term>"
    Then "<expected merchants>" should be one of the returned fallback matches
    Examples:
      | search term  | expected merchants                                 |
      | powerrangers | The Range, npower, UK Power - Compare Energy       |
      | topcat       | Europcar, Topman, PicStop.co.uk, Zipcar, Shop.com  |
      | pringles     | Coggles, Everything-LED, Princess Cruises          |
      | toasti       | Coast, Hastings Direct Car Insurance, Ticketmaster |

  @QP-512
  Scenario Outline: Searching with misspelled keywords matching anywhere in the word
    When I search for "<search term>"
    Then "<expected merchants>" should be one of the returned fallback matches
    Examples:
      | search term  | expected merchants |
      | bolking.cokm | Booking.com        |
      | argosssss    | Argos              |
