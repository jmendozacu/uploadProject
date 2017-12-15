# ProductSliderM2
A FREE Magento 2 module to create and manage product sliders.

- Built on top of fully responsive <a href="http://kenwheeler.github.io/slick/" target="_blank">Slick slider</a>
- Display products such as:
	-	**Featured**
	-	**New**
	-	**Bestsellers**
	-	**On sale**
	-	**Most viewed**

Or **mix and match** with the ones you want, or just make a **custom slider**. Choices. Good to have them.


# Features:
- ⏱ **Schedule slider** - Publish on specific date and time
- **Place anywhere** - Or exclude from an area like checkout and cart
- **Place easily** - using either XML, .phtml or a *widget*
- Display products in a default basic grid if needed
- 🎉 **Slider effects** - Choose any you like
- 🖖 **Pick and choose** - Add products manually <br/>
  For example: Your online channel just launched and Magento doesn't have a clue which your bestseller products are.
- **General settings** - One set of settings to rule them all
- **Per slider settings** - Exclude from the general rules

<br/>

**LIVE demo:**
- <a href="http://demo.ThinkIdeasdev.com/" target="_blank">Frontend demo</a>
- <a href="http://demo.ThinkIdeasdev.com/admin/" target="_blank">Backend demo</a>
<br/>
user: <strong>demo</strong>
pass: <strong>demo123</strong>

# Installation:
<h2>Step 1</h2>
- <strong>using Composer</strong>: in magento root installation folder run into the command line:<br/>
  - <strong>composer require ThinkIdeas/module-productslider</strong>
 
- <strong>or uploading files</strong>: 
	- download core ZIP file from <a href="https://github.com/ThinkIdeas/CoreM2/archive/master.zip">here</a> 
	- extract files
	- create directory in the <strong>app/code/ThinkIdeas/Core</strong>
	- upload extracted files there
	- download module ZIP file from <a href="https://github.com/ThinkIdeas/ProductsliderM2/archive/master.zip">here</a> 
	- extract files
	- create directory in the <strong>app/code/ThinkIdeas/Productslider</strong>
	- upload extracted files there

- <strong>or using Git</strong>:
	- create directory in the <strong>app/code/ThinkIdeas/Core</strong>
	- clone core module with: <strong>git clone https://github.com/ThinkIdeas/CoreM2.git .</strong>
	- create directory in the <strong>app/code/ThinkIdeas/Productslider</strong>
	- clone module with: <strong>git clone https://github.com/ThinkIdeas/ProductSliderM2.git .</strong>

<h2>Step 2</h2>
- In magento root directory run following comands into the command line:
	- bin/magento module:enable ThinkIdeas_Core
	- bin/magento module:enable ThinkIdeas_Productslider
  	- bin/magento setup:upgrade

<h2>Step 3</h2>
- Login to Magento admin and enable extension at the ThinkIdeas => Settings => General => Enable
