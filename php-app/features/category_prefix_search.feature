@QP-1728
Feature: Category prefix search
  As a Customer
  I want to get search suggestions as I type
  So that I find cashback opportunities quicker

  Scenario Outline: Categpry Prefix Match
    Given the following merchants exist:
      | 1&1                             |
      | Accorhotels                     |
      | Affordable Car Hire             |
      | ao.com                          |
      | APH Airport Parking and Hotels  |
      | Appliances Direct               |
      | Argos                           |
      | Asda                            |
      | Aviva Car Insurance             |
      | boohoo                          |
      | Booking Buddy                   |
      | Booking.com                     |
      | Boots                           |
      | Co-op Electricals               |
      | confused.com Car Insurance      |
      | Debenhams (In-store)            |
      | Dorothy Perkins                 |
      | ebookers                        |
      | Ebuyer                          |
      | EE Handset Contracts            |
      | Enterprise Rent-A-Car           |
      | Expedia                         |
      | Fasthosts                       |
      | George                          |
      | Gocompare.com Car Insurance     |
      | GoDaddy.com                     |
      | Groupon                         |
      | Hastings Direct Car Insurance   |
      | Holiday Autos                   |
      | Holiday Extras Airport Parking  |
      | Hotels.com                      |
      | Iceland                         |
      | iD Mobile                       |
      | lastminute.com                  |
      | Lunarpages UK                   |
      | Maplin Electronics              |
      | Marks & Spencer                 |
      | Microsoft Store                 |
      | Mobiles.co.uk                   |
      | Morrisons                       |
      | Namecheap                       |
      | Next                            |
      | O2 Mobiles                      |
      | Poundshop                       |
      | rentalcars.com                  |
      | River Island                    |
      | Robert Dyas                     |
      | Samsung                         |
      | Sports Direct                   |
      | Superdrug                       |
      | Thomson                         |
      | Three                           |
      | Travelodge - UK                 |
      | UK2.NET Web Hosting and Domains |
      | Viking                          |
      | Virgin Trains                   |
      | Waitrose                        |
      | Waitrose Florist                |
      | Zavvi                           |

    When I search for "<search term>"
    Then "<expected merchants>" should be part of the returned prefix matches
    Examples:
      | search term | expected merchants                                                                                        |
      | car rent    | Booking Buddy,  Expedia, Holiday Autos, rentalcars.com, Enterprise Rent-A-Car, Affordable Car Hire        |
      | supermarket | Asda, Morrisons, Waitrose, Iceland, Poundshop, Waitrose Florist                                           |
      | teleco      | Argos, BT Broadband, Sky Digital TV and Broadband, Carphone Warehouse, TalkTalk Broadband and Digital TV  |
      | car i       | Aviva Car Insurance, Gocompare.com Car Insurance, AA Car Insurance, Hastings Direct Car Insurance         |
      | accom       | Thomson, Hotels.com, Travelodge - UK, Expedia, lastminute.com, Booking.com, ebookers, LateRooms.com       |
      | car re      | Booking Buddy, Expedia, Holiday Autos, rentalcars.com, Enterprise Rent-A-Car                              |
      | superma     | Asda, Morrisons, Waitrose, Iceland, Poundshop, Approved Food                                              |
      | toys & gift | Argos, Marks & Spencer, Boots, House of Fraser, Moonpig, Mothercare, Superdrug, feelunique.com, The Works |
