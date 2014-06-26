@api
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
  Given I am not logged in
 # Given I am logged in as "devadmin" # No user with devadmin name is registered with the driver.
  Given I am logged in as a user with the "administrator" role
    Given I am viewing my "article" node with the timestamped title "behat: upload center test article"
   #   When I follow "Edit"
   #     When I click "search content"
          # And I click on "Create and Reference" tab
     #     When I click "Create and reference"

            # Emulates clicking on "Choose files" and uploading an image.
       #     When I attach the file "obama.jpg" to "multiUpload"

              # Click the "Upload" button.
              #When I press the "submitHandler" button

             # When I click "Save"

        #      When I click on XPath "//div[@id='control-panel']/descendant::button[@id='save' and @class='form-submit']"

              #When I follow "Save"

@mink:zombie
Scenario: Associate staged image with entity
  Given I am viewing the background node
 # When I follow "Edit"
 # When I click "search content"
  #When I click "Create and reference"
  #When I attach the file "obama.jpg"
  #When I click on "Save"




# Goutte at this point is not being able to handle modals very well =(
#"    When I click "Save"                                                                     # DrupalFeatureContext::assertClick()
#Link with id|title|alt|text "Save" not found.

          # TODO FIX THIS ONE
 # Then /^the "(?P<element>[^"]*)" element should not contain "(?P<value>(?:[^"]|\\")*)"$/
#- Checks, that element with specified CSS doesn't contain specified HTML.

#  Then /^the "(?P<field>(?:[^"]|\\")*)" field should contain "(?P<value>(?:[^"]|\\")*)"$/

#/^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/

 # CUSOTM CLICK WITHOUT FORMs

          #    Then I should see "obama.jpg" in the "input.text-field.title" element


  #  Then /^the "(?P<element>[^"]*)" element should contain "(?P<value>(?:[^"]|\\")*)"$/
  #Then the ".text-field.title" element should contain "obama.jpg"


 #Then the "input.text-field.title" field should contain "obama"


#- Checks, that form field with specified id|name|label|value has specified value.

              #And The image titled "obama.jpg" is staged
             # Then the "title" field should contain "obama.jpg"
              #Checks, that form field with specified id|name|label|value has specified value.


#Scenario: Associate staged image with entity
  #When I click on the "Save" button
  #When I click "Save"
  # Then the upload modal should: close | show uploaded images
  # And I click on the "close" or "save" button

  #Then The "obama.jpg" image should be associated with the entity
  #Then the "title" field should contain "obama.jpg"
    #Checks, that form field with specified id|name|label|value has specified value.

  #Then /^(?:|I )should see (?P<num>\d+) "(?P<element>[^"]*)" elements?$/
  #- Checks, that (?P<num>\d+) CSS elements exist on the page
  #Then I should see 1 ".entityreferencesearch-widget .entityreferencesearch-widget-image img" elements

  #Then the ".entityreferencesearch-widget-title" element should contain "obama.jpg"



  #Then a match is found for xpath query "(descendant-or-self::div[@class='entityreferencesearch-widget clearfix']/descendant::div[@class='entityreferencesearch-widget-title'])[1]/.[text() = 'obama.jpg']"

 # Then I should see the text "obama.jpg"


  #(descendant-or-self::div[@class='entityreferencesearch-widget clearfix']/descendant::div[@class='entityreferencesearch-widget-title'])[1]/.[text() = 'obama.jpg']


  #Then the xpath element "foo" should contain "bar"



