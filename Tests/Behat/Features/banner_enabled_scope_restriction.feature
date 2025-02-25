@fixture-OroLocaleBundle:LocalizationFixture.yml
@fixture-OroCustomerBundle:BuyerCustomerFixture.yml

Feature: Banner Scope Restriction
  In order to show a banner using oro restrictions
  As an Admin
  I need to be able to manage localization, customerGroup and customer fields

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session  |
      | Buyer | second_session |

  Scenario: Create an active banner with localization restriction
    Given I proceed as the Admin
    When I login as administrator
    And I go to Marketing / Banner
    And I should see "There are no banners"
    Then I click "Create Banner"
    And I fill "Banner Form" with:
      | Title           | My First Banner With Locale Restriction                 |
      | Enabled         | true                                                    |
      | Content Variant | My first banner content default with locale restriction |
      | Localization    | Localization1                                           |
    And I save and close form
    Then I should see "Your banner has been saved" flash message
    Then I proceed as the Buyer
    Then I go to the homepage
    Then I should not see "My first banner content default with locale restriction"

  Scenario: Create an active banner with customer group restriction
    Given I proceed as the Admin
    When I login as administrator
    And I go to Marketing / Banner
    And I should see "There are no banners"
    Then I click "Create Banner"
    And I fill "Banner Form" with:
      | Title           | My First Banner With Customer Group Restriction                 |
      | Enabled         | true                                                            |
      | Content Variant | My first banner content default with customer group restriction |
      | Customer Group  | Group with PriceList                                            |
    And I save and close form
    Then I should see "Your banner has been saved" flash message
    Then I proceed as the Buyer
    Then I go to the homepage
    Then I should not see "My first banner content default with customer group restriction"
    Then I login as AmandaRCole@example.org the "Buyer" at "second_session" session
    Then I go to the homepage
    Then I should see "My first banner content default with customer group restriction"

  Scenario: Create an active banner with customer restriction
    Given I proceed as the Admin
    When I login as administrator
    And I go to Marketing / Banner
    And I should see "There are no banners"
    Then I click "Create Banner"
    And I fill "Banner Form" with:
      | Title           | My First Banner With Customer Restriction              |
      | Enabled         | true                                                   |
      | Content Variant | My first banner content default customer 1 restriction |
      | Customer        | first customer                                         |
    And I save and close form
    Then I should see "Your banner has been saved" flash message
    Then I login as MarleneSBradley@example.com the "Buyer" at "second_session" session
    Then I go to the homepage
    Then I should not see "My first banner content default customer 1 restriction"
    Then I login as AmandaRCole@example.org the "Buyer" at "second_session" session
    Then I go to the homepage
    Then I should see "My first banner content default customer 1 restriction"