Feature: Banner Enabled Home Sticky
  In order to show a banner on the homepage only and sticky
  As an Admin
  I need to be able to create such a banner on the back office

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session |

  Scenario: Create an active banner, homepage only and sticky
    Given I proceed as the Admin
    When I login as administrator
    And I go to Marketing / Banner
    And I should see "There are no banners"
    Then I click "Create Banner"
    And I fill "Banner Form" with:
      | Title           | My First Banner                 |
      | Enabled         | true                            |
      | Content Variant | My first banner content default |
      | Home Page Only  | true                            |
      | Sticky          | true                            |
    And I save and close form
    Then I should see "Your banner has been saved" flash message
    And I should see "My First Banner"
    Then I go to the homepage
    Then I should see "My first banner content default"
    Then I click "About"
    Then I should not see "My first banner content default"
    Then I go to the homepage
    When I scroll to bottom
    Then "sticky" class should be present

  Scenario: Create an active banner and sticky
    Given I proceed as the Admin
    When I login as administrator
    And I go to Marketing / Banner
    And I should see "There are no banners"
    Then I click "Create Banner"
    And I fill "Banner Form" with:
      | Title           | My First Sticky Banner                 |
      | Enabled         | true                                   |
      | Content Variant | My first sticky banner content default |
      | Sticky          | true                                   |
    And I save and close form
    Then I should see "Your banner has been saved" flash message
    And I should see "My First Sticky Banner"
    Then I go to the homepage
    Then I should see "My first sticky banner content default"
    Then I click "About"
    Then I should see "My first sticky banner content default"
    When I scroll to bottom
    Then "sticky" class should be present