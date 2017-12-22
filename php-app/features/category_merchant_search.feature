Feature: Category Merchant Search
  As a customer who is searching for a retailer in a category
  I want the retailers to be ordered by popularity
  So that I can easily find merchants that are popular for the product or service I am looking for

  Background:
    Given the following merchants exist:
      | 32Red Online Casino                  |
      | Accorhotels                          |
      | Argos                                |
      | Marks & Spencer                      |
      | ASOS                                 |
      | Betfair Sportsbook                   |
      | Booking Buddy                        |
      | Booking.com                          |
      | British Airways Flights and Holidays |
      | Debenhams (In-store)                 |
      | easyJet Holidays                     |
      | ebookers                             |
      | Endsleigh Car Insurance              |
      | Expedia                              |
      | Gocompare.com Car Insurance          |
      | Hotels.com                           |
      | House of Fraser                      |
      | Ladbrokes Sportsbook                 |
      | lastminute.com                       |
      | LateRooms.com                        |
      | Lottoland                            |
      | M&S Car Insurance                    |
      | National Lottery                     |
      | New Look                             |
      | Next                                 |
      | Sports Direct                        |
      | Tesco Bank Car Insurance             |
      | Thomson                              |
      | Travelodge - UK                      |

  @QP-1615
  Scenario Outline: Category name match
    When I search for merchants by "<category name>" in order of popularity
    Then "<expected merchants>" should be part of the returned category matches
    Examples:
      | category name | expected merchants                                                                                                              |
      | accommodation | Hotels.com, Expedia, Booking.com, Travelodge - UK, Booking Buddy, lastminute.com, ebookers, LateRooms.com, Thomson, Accorhotels |
      | flights       | Expedia, Booking Buddy, lastminute.com, ebookers, Thomson, British Airways Flights and Holidays, easyJet Holidays               |
      | clothing      | Marks & Spencer, ASOS, Next, House of Fraser, New Look, Very, JD Sports (In-store), JD Sports                                   |
      | car insurance | Gocompare.com Car Insurance, AXA Car Insurance, Tesco Bank Car Insurance, Endsleigh Car Insurance, M&S Car Insurance            |
      | gambling      | Betfair Sportsbook, Ladbrokes Sportsbook, National Lottery, 32Red Online Casino, Lottoland, Mybet UK                            |