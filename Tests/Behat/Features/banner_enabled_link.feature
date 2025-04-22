Feature: Banner Link
  In order to show a banner redirecting to some external content
  As an Admin
  I need to be able to create a banner and add a link on it

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session |

  Scenario: Create an active banner with default link
    Given I proceed as the Admin
    When I login as administrator
    And I go to Marketing / Banner
    And I should see "There are no banners"
    Then I click "Create Banner"
    And I fill "Banner Form" with:
      | Title           | My First Banner                       |
      | Enabled         | true                                  |
      | Content Variant | My first banner content default       |
      | Link Variant    | https://www.norsys.fr/qui-sommes-nous |
    And I save and close form
    Then I should see "Your banner has been saved" flash message
    Then I go to the homepage
    Then I should see "My first banner content default"
    Then I click "Banner Link"
    Then a new browser tab is opened and I switch to it
    Then the url should match "/qui-sommes-nous"