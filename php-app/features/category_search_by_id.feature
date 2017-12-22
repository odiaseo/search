Feature: Category Exact match search
  As a Customer
  I want to find merchants in the category I type
  So that I find cashback opportunities relevant to the category

  Background:
    Given the following merchants exist:

      | Marks & Spencer        |
      | ASOS                   |
      | House of Fraser        |
      | Match.com              |
      | eHarmony               |
      | matchaffinity          |
      | DoingSomething.co.uk   |
      | B&Q                    |
      | Wickes                 |
      | Wilko                  |
      | Go Outdoors            |
      | Go Outdoors (In-store) |
      | Zavvi                  |
      | CD Keys                |
      | iTunes                 |
      | HMV                    |
      | Music Magpie           |
      | The Hut                |
      | Mankind                |
      | Argos                  |
      | Boots                  |
      | Marks & Spencer        |
      | House of Fraser        |

  @QP-1885
  Scenario Outline: Category search by id
    When I search for "<category name>" by id
    Then "<expected merchants>" should be part of the returned category matches
    Examples:
      | category name | expected merchants                                       |
      | footwear      | Marks & Spencer, ASOS, House of Fraser                   |
      | dating        | Match.com, eHarmony, matchaffinity, DoingSomething.co.uk |
      | garden        | B&Q, Wickes, Wilko, Go Outdoors, Go Outdoors (In-store)  |
      | music         | Zavvi, CD Keys, iTunes, HMV, Music Magpie, The Hut       |
      | toys & gifts  | Argos, Boots, Marks & Spencer, House of Fraser           |