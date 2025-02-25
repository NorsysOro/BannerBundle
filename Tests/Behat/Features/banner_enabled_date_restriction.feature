Feature: Banner Date Restriction
  In order to show a banner using date restrictions
  As an Admin
  I need to be able to manage a banners date

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session |

  Scenario: Create banners that don't match date restrictions
    Given I proceed as the Admin
    When I login as administrator
    And I go to Marketing / Banner
    And I should see "There are no banners"
    Then I click "Create Banner"
    And I fill "Banner Form" with:
      | Title           | My First Active Past Banner                 |
      | Enabled         | true                                        |
      | Content Variant | My first active past banner content default |
      | Start           | Nov 24, 2020                                |
    And I save and close form
    Then I should see "Your banner has been saved" flash message
    And I go to Marketing / Banner
    Then I click "Create Banner"
    And I fill "Banner Form" with:
      | Title           | My Second Active Past Banner                 |
      | Enabled         | true                                         |
      | Content Variant | My second active past banner content default |
      | Start           | Dec 24, 2020                                 |
    When I save and close form
    Then I should see "There is already an active banner with these constraints" error message

  Scenario: Create banners that match date restrictions
    Given I proceed as the Admin
    When I login as administrator
    And I go to Marketing / Banner
    And I should see "There are no banners"
    Then I click "Create Banner"
    And I fill "Banner Form" with:
      | Title           | My First Active Future Banner                 |
      | Enabled         | true                                          |
      | Content Variant | My first active future banner content default |
      | Start           | Jan 24, 2023                                  |
      | End             | Feb 24, 2023                                  |
    And I save and close form
    Then I should see "Your banner has been saved" flash message
    And I go to Marketing / Banner
    Then I click "Create Banner"
    And I fill "Banner Form" with:
      | Title           | My Second Active Future Banner                 |
      | Enabled         | true                                           |
      | Content Variant | My second active future banner content default |
      | Start           | Nov 24, 2023                                   |
      | End             | Dec 24, 2023                                   |
    When I save and close form
    Then I should see "Your banner has been saved" flash message