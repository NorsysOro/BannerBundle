Feature: Banner Enabled
  In order to show or not a banner on the frontend
  As an Admin
  I need to be able to create and disable a banner on the back office

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session |

  Scenario: Create an inactive banner
    Given I proceed as the Admin
    When I login as administrator
    And I go to Marketing / Banner
    And I should see "There are no banners"
    Then I click "Create Banner"
    And I fill "Banner Form" with:
      | Title           | My First Disabled Banner                 |
      | Enabled         | false                                    |
      | Content Variant | My first disabled banner content default |
    And I save and close form
    Then I should see "Your banner has been saved" flash message
    Then I go to the homepage
    Then I should not see "My first banner content default"

  Scenario: Create an active banner
    Given I proceed as the Admin
    When I login as administrator
    And I go to Marketing / Banner
    Then I click "Create Banner"
    And I fill "Banner Form" with:
      | Title           | My First Enabled Banner                 |
      | Enabled         | true                                    |
      | Content Variant | My first enabled banner content default |
    And I save and close form
    Then I should see "Your banner has been saved" flash message
    Then I go to the homepage
    Then I should see "My first enabled banner content default"