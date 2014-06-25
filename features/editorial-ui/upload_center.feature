Feature: Upload Center
  In order to upload only desired images
  As a editor
  I should be able to upload images
    Before associating uploaded images to an entity
      I should be able to cancel a handful of uploaded images
      I should be able to cancel all upload images
      I should be able to upload more images after cancelling all uploads
      I should be able to upload more images without closing the upload modal window
  I should be able to associate currently uploaded images to an entity at any time
  I should not be able to see and associate to an entity removed images

# ICS-586: Entity Create and Reference goes blank after deleting an uploaded
# image and there is no option to upload the images.

# The background is run for every scenario
Background:
  Given I create an "article" entity
    And I click on "search content" under the "article images" field
    And I click on "Create and Reference" tab
    And I click on "Choose files"
    And I upload an image titled "obama.jpg"
    And I click on the "Upload" button
    And The image titled "obama.jpg" is staged

Scenario: Associate staged image with entity
  When I click on the "Save" button
  # Then the upload modal should: close | show uploaded images
  # And I click on the "close" or "save" button
  Then The "obama.jpg" image should be associated with the entity

Scenario: Delete staged image without associating it to an entity
  When I click on the "Delete" button
  Then The upload center modal should show up

Scenario: Staged images do not show up on search when cancelled
  Given I click on the "Delete" button
  And The upload center modal shows up
  When I click on the "reference" tab
  Then The cancelled image should not show up on search

Scenario: Staged images do not show up on search when cancelled
  Given I click on the "Delete" button
  And The upload center modal shows up
  And I click on the "Close" button
  Then The cancelled image should not be associated with an entity



Scenario: Upload image via drag and drop
  # @todo currently not working
  # @todo specify test case