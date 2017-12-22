Feature: Best match search
  As a Customer
  I want to get search suggestions as I type
  So that I find cashback opportunities quicker   |

  @QP-508
  Scenario Outline: Search with keywords matching a middle of word
    When I search for "<search term>"
    Then "<expected merchants>" should be part of the returned best matches
    Examples:
      | search term | expected merchants                                                                                            |
      | rance       | Air France, M&S Car Insurance, LV= Car Insurance,  AXA Car Insurance, Aviva Home Insurance, AA Car Insurance  |
      | orld        | WorldSIM, Cineworld, BedroomWorld, Ideal World, Walt Disney World                                             |
      | tore        | Halfords (In-store), JD Sports (In-store), Debenhams (In-store), Ernest Jones (In-store), Vodafone (In-store) |
      | oom         | Room4, Easy Bathrooms, UKBathroomStore.co.uk, BedroomWorld, Musicroom.com                                     |

  @QP-510
  Scenario Outline: Matching merchant keywords
    Given the following merchants exist with associated keywords:
      | merchant   | keyword          |
      | Gameseek   | grand theft auto |
      | Pharmacy2U | Pharmacy to you  |
      | 888Casino  | online casino    |
    When I search for "<search query>"
    Then "<expected merchant>" should be part of the returned best matches
    Examples:
      | search query         | expected merchant |
      | grand theft auto     | Gameseek          |
      | pharmacy to you      | Pharmacy2U        |
      | online casino        | 888Casino         |
      | marks with spwe;ncer | Marks & Spencer   |
      | mrks and spencer     | Marks & Spencer   |
      | lkttlewoods          | Littlewoods       |

  @QP-762
  Scenario: In-Store Match
    Given the following merchants exist:
      | Halfords (In-store)  |
      | Debenhams (In-store) |
      #| JD Sports (In-store) |
      | Vodafone (In-store)  |
    When I search for "instore"
    Then the following merchants should be part of returned best matches:
      | Halfords (In-store)  |
      | Debenhams (In-store) |
     # | JD Sports (In-store) |
      | Vodafone (In-store)  |