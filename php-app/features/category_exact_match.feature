Feature: Category Exact match search
  As a Customer
  I want to find merchants in the category I type
  So that I find cashback opportunities relevant to the category

  Background:
    Given the following merchants exist:
      | achica                  |
      | adidas shop             |
      | argos                   |
      | asos                    |
      | b&q                     |
      | bbc store               |
      | boots                   |
      | cd keys                 |
      | clarks                  |
      | datingdirect            |
      | dfds seaways            |
      | direct ferries          |
      | doingsomething.co.uk    |
      | eharmony                |
      | eventim uk              |
      | gardening direct        |
      | go outdoors (in-store)  |
      | go outdoors             |
      | gtech                   |
      | hive.co.uk              |
      | hmv                     |
      | house of fraser         |
      | ideal world             |
      | irish ferries           |
      | itunes                  |
      | lovestruck.com          |
      | mankind                 |
      | marks & spencer         |
      | match.com               |
      | matchaffinity           |
      | moonpig                 |
      | music magpie            |
      | musicroom.com           |
      | new look                |
      | george                  |
      | nike store              |
      | p&o ferries             |
      | royal caribbean cruises |
      | sports direct           |
      | stena line              |
      | studio                  |
      | the guardian soulmates  |
      | the hut                 |
      | the range               |
      | the single solution     |
      | wickes                  |
      | wightlink               |
      | wilko                   |
      | zavvi                   |
      | ziffit                  |

  @QP-910
  Scenario Outline: Category name prefix match
    When I search for "<search term>"
    Then "<expected merchants>" should be part of the returned prefix matches
    Examples:
      | search term  | expected merchants                                                                                                          |
      | footwear     | Marks & Spencer, ASOS, House of Fraser, Sports Direct, New Look, George, Clarks, adidas shop, Nike Store                    |
      | electricals  | Argos, Currys PC World, Boots, House of Fraser, Very, ao.com, Superdrug, Carphone Warehouse, Wilko                          |
      | finance      | Post Office Travel Money, Travelex, Western Union, Post Office Credit Cards, Tesco Bank No Balance Transfer Fee Credit Card |
      | wearables    | Argos, Currys PC World, Boots, Very, Halfords (In-store), Halfords, Ali Express, Zavvi, Viking, Apple, Samsung,  Hughes     |
      | toys & gifts | Argos, Boots, Marks & Spencer, House of Fraser, Moonpig, notonthehighstreet.com, Superdrug, Mothercare, feelunique.com      |