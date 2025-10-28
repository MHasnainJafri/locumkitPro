<?php

namespace Database\Seeders;

use App\Models\IndustryNews;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class IndustryNewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        IndustryNews::truncate();
        Schema::enableForeignKeyConstraints();

        $industry_news = array(
            array('title' => 'IR35 &ndash; a practical guide to HMRC compliance', 'slug' => 'ir35-a-practical-guide-to-hmrc-compliance', 'description' => '<p>Since 2017 practices have been responsible for deciding whether contractors fulfil IR35 status. This, potentially confusing, piece of legislation is causing headaches for practices and locum who are unsure of the rules.</p>
          
          <p>IR35 is legislation which focuses on the employment status of workers who are not paid via the payroll; it is used by HMRC to obtain employer&rsquo;s National Insurance Contributions (NIC), as well as PAYE, plus employee&rsquo;s NIC from such workers &ndash; including locum GPs &ndash; in cases where HMRC deems that they should be taxed as an employee.</p>
          
          <p>Essentially, IR35 looks to rebalance the tax savings that can be achieved by self-employed locum GPs or locums who use a limited company to provide their work. It&rsquo;s a confusing area that can cause concerns for those caught in its web &ndash; including GP practices.</p>
          
          <p><strong>Who does it apply to?</strong></p>
          
          <p>In the past it was down to the locums themselves to assess whether or not they were caught by IR35; however, since 6 April 2017 it has been the GP practices that have needed to assess the position.</p>
          
          <p><strong>How do I check whether our locums are caught by IR35?</strong></p>
          
          <p>There is an <a href="https://www.tax.service.gov.uk/check-employment-status-for-tax/setup" target="_blank">online status checker</a> which practices can use to assess the employment status of locum staff.</p>
          
          <p>Working through the checker there are a series of questions that explore the relationship between the practice and the locum; depending on the answer to each question, further questions are then asked in an attempt to determine the employment status of the contract.</p>
          
          <p>The output of the checker will show one of three outcomes: &lsquo;unable to determine the position&rsquo;, &lsquo;classed as employed for tax purposes&rsquo;, or &lsquo;classed as self-employed for tax purposes&rsquo;.</p>
          
          <p>If a locum providing their services via a limited company is deemed to be employed by the practice then the practice <em>must</em> deduct tax from the payment to the locum&rsquo;s company and pay that money to HMRC. The rate is 32% and comprises basic rate tax at 20%, plus employee&rsquo;s NIC at 12%, together with any employer&rsquo;s NIC.</p>
          
          <p>The worker then gets credit for the tax deducted at source when declaring their taxes. The ultimate result is that employer&rsquo;s NIC is paid to HMRC and taxes are paid at personal tax rates rather than the lower corporate tax rates.</p>
          
          <p><strong>Being inside IR35 sounds like a headache; how do I ensure that the locums are outside of IR35?</strong></p>
          
          <p>Ultimately, the decision as to whether or not a worker is inside IR35 rests on the facts. The key questions from the online checker are:</p>
          
          <ul>
              <li>Q: If the worker&rsquo;s business sent someone else to do the work (a substitute) and they met all the necessary criteria, would the end-client ever reject them?</li>
          </ul>
          
          <p>If the practice would accept the substitute then it is much more likely that the contract would fall <em>outside</em> of IR35.</p>
          
          <ul>
              <li>Q: What does the worker have to provide for this engagement that they can&rsquo;t claim as an expense from the end-client or an agency &ndash; eg. materials, vehicle, equipment, other expenses &ndash; ?</li>
          </ul>
          
          <p>If the answer is that the practice supplies all the items they need then it increases the likelihood that the contract <em>would</em> fall within IR35. By contrast, insisting the locum brings their own, fully-equipped, doctor&rsquo;s bag and uses their own car for home visits increases the likelihood the contract will fall <em>outside</em> of IR35.</p>
          
          <p>The practice cannot contrive a position by which the contract falls outside of IR35 just because this is more convenient; the decision must be based upon the facts of the engagement in question.</p>
          
          <p>If a practice genuinely changes their stance &ndash; for example, from not allowing a substitute to allowing one &ndash; this may be sufficient to change the result; the practice would then, naturally, have to stand by this if the situation ever arose.</p>
          
          <p><strong>I work directly for practices as a sole trader, does this apply to me?</strong></p>
          
          <p>No. As there is no intermediary between you and the client, you are excluded from IR35. In this case, the issue is solely with regard to employment status.</p>
          
          <p><strong>I get my work through an agency, does this change apply to me?</strong></p>
          
          <p>If you work as a sole trader via an agency, then IR35 needs to be considered, as the agency could be considered an intermediary. If you work via an agency through your own limited company, then IR35 still needs to be considered even though there is more than one intermediary between you and the engager. In both cases, the responsibility for assessing whether IR35 applies and applying appropriate tax and NI lies with the agency, rather than the public sector body.</p>
          
          <p><strong>What happens if I don&rsquo;t check the status?</strong></p>
          
          <p>If HMRC assesses that workers at the practice are inside IR35 but tax payments are not being deducted then, since April 2017, the back tax due would fall upon the practice rather than the worker &ndash; so it is of paramount importance that practices check the IR35 status of every worker not on the payroll.</p>
          
          <p><strong>What should I do today?</strong></p>
          
          <p>If you have not already done so, the online checker should be used for any contracts in place and any new ones in the future. This is a complex &ndash; and potentially costly &ndash; area and so expert advice should be sought if there is any uncertainty. You can also contact our own inhouse dual qualified locum and Chartered Accountant at mochaudhry@locumkit.com for any questions you may have in this regards.</p>
          
          <p>&nbsp;</p>', 'image_path' => '/media/files/industry_news/42cf56a61f16a300ae3f3e4b596825c1_IR35.png', 'user_type' => '2,3', 'category_id' => '3', 'status' => '0', 'created_at' => '2017-07-07 21:14:30', 'metatitle' => 'IR35 â€“ a practical guide to HMRC compliance | Locumkit', 'metadescription' => 'IR35 â€“ a practical guide to HMRC compliance', 'metakeywords' => 'Locumkit'),
            array('title' => 'Should you set up a limited company for locum work?', 'slug' => 'should-you-set-up-a-limited-company-for-locum-work', 'description' => '<p><strong>What is a limited company?</strong></p>
          
          <p>A limited company has its own legal identity and is treated as a &lsquo;person&rsquo;. As a shareholder your liability is limited (hence the name &ndash; &lsquo;limited by shares&rsquo;). The company is run by directors and has shareholders. As an individual locum, you can be both. You will need to have a designated bank account in the name of the company.</p>
          
          <p>Other shareholders could include your spouse/partner, but only if they are going to do some work for the company. However if they do only a small amount, the shareholdings should reflect this.</p>
          
          <p><strong>How do I pay tax as a limited company?</strong></p>
          
          <p>The main difference between a limited company and sole trader status is tax.</p>
          
          <p>Companies pay 20% tax on profits via the Corporation Tax scheme. However you will have further tax to pay when you take money out of the company.</p>
          
          <p>You will pay tax as an individual and an employee of the company. You can also receive dividend payments, which you also have to pay tax on. As of April 2018 tax on the first &pound;2,000 is at 0% and then after this tax is paid dependent on which income tax band you are in. A basic rate tax payer will pay 7.5%, a higher rate tax payer will pay 32.5% and an additional rate tax payer will pay 38.1%.</p>
          
          <p><strong>Pros of setting up a limited company</strong></p>
          
          <p>The personal liability of shareholders is restricted to the face value of the shares they have subscribed to. Workers (directors) are paid a salary (taxed via Pay as You Earn at source) and this cost is incurred as a company expense. These expenses can then be deducted prior to paying company tax &ndash; remember the company is a &lsquo;person&rsquo; in its own right and pays tax as any other person would. Any residual profits can then be paid to shareholders as additional income in the form of dividends. As previously mentioned you can include your partner or spouse on the books and give them a salary and some shares. This can provide some significant tax savings, especially if they are a basic rate tax payer.&nbsp;</p>
          
          <p><strong>Cons of setting up a limited company</strong></p>
          
          <p>As with working for a locum agency, you cannot use your wages paid through a company to contribute into the NHS Pension Scheme so you will lose the pension benefit and the tax relief on this.</p>
          
          <p>You will also have to make yearly submissions of legally prepared company accounts and an annual return at Companies House where they are published and accessible by anyone who wishes to view your data.</p>
          
          <p>Expense calculations can also be a bit trickier. As a limited company you are an employee so expenses claimed must be wholly, exclusively and necessary for the business. For example, in the case of mileage claims, you have to submit an expense claim to the company like any other employee. The company then reimburses your cost, but this is seen as a benefit in kind for you as the employee, involving additional paperwork for the company in the form of a PD11 and your having to pay tax on this benefit. In contrast, a self-employed (sole trader) locum benefits from much more relaxed expense rules, although they do still have to be wholly and exclusively for business purposes.</p>
          
          <p>If your company turnover exceeds &pound;83,000 your business will need to charge VAT on any invoices. Also, if you are performing a HR/Office/Managerial advisor locum position, where you are not treating patients directly, you will need to charge standard VAT rate for your service. Visit the <a href="https://www.gov.uk/guidance/health-professionals-pharmaceutical-products-and-vat-notice-70157" target="_blank">HMRC website</a> for a full breakdown on how VAT is applicable for locum doctors.</p>
          
          <p>You can also also contact our own inhouse dual qualified locum and Chartered Accountant at mochaudhry@locumkit.com for any questions you may have in this regards.</p>
          
          <p>&nbsp;</p>
          
          <p>&nbsp;</p>', 'image_path' => '/media/files/industry_news/428c424ed9f015706500e846e59317a8_st ltd.jpg', 'user_type' => '2,3', 'category_id' => '1,8,3,4', 'status' => '0', 'created_at' => '2017-07-08 14:57:42', 'metatitle' => 'Should you set up a limited company for locum work | Locumkit', 'metadescription' => 'A limited company has its own legal identity and is treated as a â€˜personâ€™. As a shareholder your liability is limited (hence the name â€“ â€˜limited by sharesâ€™). The company is run by directors and has shareholders. As an individual locum, you can b', 'metakeywords' => 'Locumkit'),
            array('title' => 'In the era of Warby Parker, measure your own eyeglass prescription at home', 'slug' => 'in-the-era-of-warby-parker-measure-your-own-eyeglass-prescription-at-home', 'description' => '<p>With the popularity of direct-to-consumer retailers like Warby Parker, Zenni Optical and EyeBuyDirect, people are taking the process of buying prescription glasses into their own hands by skipping brick-and-mortar retailers and insurance policies.</p>
          
          <p>But EyeQue VisionCheck, which debuted here at <a href="https://www.cnet.com/ces/">CES</a> 2019 on Sunday, is hoping to do one better by skipping the optometrist altogether. Working with a mobile app, the Bluetooth-powered portable device can conduct vision tests at home, and can measure nearsightedness, farsightedness and astigmatism.&nbsp;</p>
          
          <p>After testing, the device spits out what it calls your personal &quot;EyeGlass Numbers.&quot; The numbers work similar to an eyeglass prescription, but do not require a doctor&#39;s sign-off. You can then take your number to specific retailers that will honor EyeGlass numbers like Zenni Optical and EyeBuyDirect, and buy prescription glasses completely on your own -- no appointment or waiting room required.</p>
          
          <p><img alt="eyeque-visioncheck-product-photos-1" src="https://www.cnet.com/news/in-the-era-of-warby-parker-measure-your-own-eyeglass-prescription-at-home/" style="height:0px; width:570px" /></p>
          
          <p>The updated EyeQue VIsionCheck.</p>
          
          <p>Tyler Lizenby/CNET</p>
          
          <p>During my brief time with it, I tried getting an accurate reading of my vision. To do this, I had to align a red and green line that I viewed through the scope until they were touching. Using the buttons on top of the VisionCheck, I pushed the lines either closer or farther apart. The number of times I clicked these buttons determined my EyeGlass numbers.&nbsp;</p>
          
          <p>Because the entire process takes up to 20 minutes, I couldn&#39;t finish the test and didn&#39;t get an accurate reading. Plus, I found the initial process to be a bit complicated. I could see how this device might be convenient for someone who needs to constantly check their changing prescription, but as a casual eyeglass wearer, a once-in-a-year trip to the doctor suffices for me.</p>
          
          <p>EyeQue VisionCheck is the newer iteration of last year&#39;s <a href="https://www.cnet.com/news/test-your-vision-at-home-with-the-eyeque-for-23-99-shipped/">EyeQue</a>, which is currently available online at <a href="https://www.cnet.com/tags/amazon/">Amazon</a> and BestBuy for $30. Unlike its predecessor, VisionCheck has Bluetooth, and it can automatically measure the distance between internal optical lenses without the need to manual rotate the eye cups.</p>
          
          <p>The&nbsp;<a href="https://www.indiegogo.com/projects/eyeque-visioncheck-world-s-1st-automated-eye-test#/" target="_blank">Indiegogo campaign</a>&nbsp;for VisionCheck has already exceeded its goal and the company plans to ship out the product in March or sooner.</p>
          
          <p>&nbsp;</p>', 'image_path' => '/media/files/industry_news/c8c48f014d47b006ed990b47fef2a529_Capture.JPG', 'user_type' => '2,3', 'category_id' => '1,8,3,4', 'status' => '0', 'created_at' => '2017-07-08 15:03:24', 'metatitle' => 'In the era of Warby Parker, measure your own eyeglass prescription at home | Locumkit', 'metadescription' => 'With the popularity of direct-to-consumer retailers like Warby Parker, Zenni Optical and EyeBuyDirect, people are taking the process of buying prescription glasses into their own hands by skipping brick-and-mortar retailers and insurance policies.', 'metakeywords' => 'Locumkit'),
            array('title' => 'Controversial IR35 tax reforms in UK delayed for a year', 'slug' => 'ir35-delayed-until-april-2021', 'description' => '<p>With all the sad news we are facing at the moment, there is some good news for the self employed - the controversial IR35 tax reforms have been delayed for a year. A welcome piece of news for locums and employers who use locums.</p>
          
          <p>Speaking in the House of Commons Budget debate tonight Chief Treasury Secretary Steve Barclay said: &quot;This is a deferral in response to the ongoing spread of Covid-19 to help businesses and individuals. This is a deferral, not a cancellation, and the government remains committed to re-introducing this policy.&quot;</p>
          
          <p>The AOP has also recently released a newsletter updating all its members on the delay of IR35 and advised locums to discuss with their employers, if they had a change in contract in anticipation of IR35.</p>
          
          <p>Please do note this means that the employer is no longer required to assess your employment status but you as a locum are still required to assess your own status. HMRC can ask you to pay additional tax for previous periods, if they deem you to be a &#39;pseudo employee&#39;.</p>
          
          <p>If you have any questions please do not hesistate to contact me at <a class="feed-shared-text-view__email ember-view" href="mailto:mochaudhry@locumkit.com" id="ember841" rel="noopener noreferrer" tabindex="0" target="_blank">mochaudhry@locumkit.com</a> Locumkit - Specialist Accountants for Optoms</p>', 'image_path' => '/media/files/industry_news/c7ffb758ec8b53c3b37cb05ea63f0401_IR35.PNG', 'user_type' => '2,3', 'category_id' => '8,3', 'status' => '1', 'created_at' => '2020-03-19 11:32:02', 'metatitle' => 'Controversial IR35 tax reforms in UK delayed for a year', 'metadescription' => 'With all the sad news we are facing at the moment, there is some good news for the self employed - the controversial IR35 tax reforms have been delayed for a year. A welcome piece of news for locums and employers who use locums.', 'metakeywords' => ''),
            array('title' => 'Advise on COVID-19', 'slug' => 'safety-advise-on-covid-19', 'description' => '<div dir="auto">To all locums,</div>
          
          <div dir="auto">&nbsp;</div>
          
          <div dir="auto">We hope you are all healthy and well. In light of the current COVID-19 situation and the impact it could have on the industry, we wanted to address a few issues.</div>
          
          <div dir="auto">&nbsp;</div>
          
          <div dir="auto">We understand the concerns you have about cancelled clinics and lost earnings. However, due to the close proximity we have with patients and the potential high number of elderly or vulnerable patients we see, it is vital that you please follow the guidance set out by Public Health England and the NHS. If you present with symptoms of COVID-19, no matter how slight, you should not attend work and self isolate. Moreover if anyone in your household presents with symptoms you should also self isolate yourself. The latest advice on how long to isolate for can be found here:&nbsp;&nbsp;</div>
          
          <div dir="auto">&nbsp;</div>
          
          <div dir="auto"><a href="https://www.nhs.uk/conditions/coronavirus-covid-19/">https://www.nhs.uk/conditions/coronavirus-covid-19/</a>&nbsp;</div>
          
          <div dir="auto">&nbsp;</div>
          
          <div dir="auto">If you need to cancel work because you have been advised to self-isolate (regardless of whether you have booked with us or not) it is vital to let the organisations involved know as soon as possible. Cancellations at this time will not impact you negatively. Alternatively, please understand we may have to cancel your clinics, sometimes at very short notice, due to the evolving situation.</div>
          
          <div dir="auto">&nbsp;</div>
          
          <div>Thank you</div>
          
          <div>&nbsp;</div>
          
          <div>The LocumKit team</div>', 'image_path' => '/media/files/industry_news/a5b9e2550f4a277f118aa081400bc556_Virus.PNG', 'user_type' => '2', 'category_id' => '8,3', 'status' => '1', 'created_at' => '2020-03-19 11:34:57', 'metatitle' => 'Advise on COVID-19', 'metadescription' => 'We hope you are all healthy and well. In light of the current COVID-19 situation and the impact it could have on the industry, we wanted to address a few issues', 'metakeywords' => ''),
            array('title' => 'Budget 2020', 'slug' => 'a-summary-of-key-points-of-budget-2020-covid-19-budget', 'description' => '<p style="text-align:center"><span style="font-family:trebuchet ms,helvetica,sans-serif"><span style="font-size:14px"><span style="color:#000000">The 2020 Spring Budget was heavily dominated by the issue of Coronavirus and of how to protect the economy against its effects. The government has outlaid many proposals for the employed and small to medium size businesses to overcome any interruption to trade.</span></span></span></p>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><span style="color:#0000FF"><strong><u>COVID-19 BUDGET</u></strong></span></span></p>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>Business Rates Relief</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">The Government has announced a freeze for 12 months for all businesses operating in the leisure, hospitality and retail sectors</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">If you are a business who falls in the above category, then you will automatically receive amended rates bills from your local authority</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>Small Business Grant Funding</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">If you qualify for the small business rates relief or rural rate relief then you may be applicable to apply for a one-off &pound;10,000 grant. This is to aid your business potential downturn as a result of Coronavirus</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Businesses that operate in the leisure, hospitality and retail sectors and have a rateable value between &pound;15,000 and &pound;51,000 are eligible to apply for a grant of up to &pound;25,000</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Contact your accountant and local authority to apply for this grant</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>Business Interruption Loan Scheme</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">The Government has announced Government backed loans to a value of &pound;330bn</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">These loans will be on favourable terms, with no interest payable in the first 6 months</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>Payroll</strong></span></p>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><u>For Resident Optoms</u></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Statutory Sick Pay (SSP) will be paid from the first day of sickness, rather than the fourth day for affected individuals and will include those infected and those self-isolating (even if they do not have the symptoms of the disease)</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><u>For Employers</u></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">If you are a small to medium size business (less than 250 staff as of Feb 2020), you can claim refunds for sick pay payments made to staff for two weeks, if approved SSP payments. A GP note is not required but all record of staff absences are to be kept</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">The actual method for making a claim is yet to be agreed as current payroll processes cannot accommodate this type of refund</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><u>For Locums</u></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">As Locums, we are not eligible to SSP &ndash; For those who do not have income protection, the Government has provided easier access to Universal Credits and the Contributory Employment and Support Allowance</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><u>For all </u></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">He Chancellor has confirmed that mortgage lenders have agreed to allow a period of months grace on any mortgage repayments. This will be reviewed again in due course.</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">If you pay rent, then at the moment there is no real announcement other than if you are struggling, to contact your local authority</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Some banks have also increased credit card and cash withdrawal limits</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>HMRC&rsquo;s Time to Pay </strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">HMRC have expanded their existing Time to Pay arrangements in order to assist businesses that have been affected by COVID-19. You can now defer your tax payments (including PAYE and VAT) and agree to pay by instalments</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">HMRC have set up a helpline to specifically to deal with this: 0800 0159 559</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>Insurance</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">The Government has announced that if leisure/hospitality/retail businesses have insurance which covers pandemics then they will be able to claim due to the measures recently introduced by the Government</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>Companies House</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Companies can apply for an extension if they are unable to file their accounts on time due to coronavirus</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Extensions are generally given for up to 30 days. Guidance on how to apply for this extension is included below:</span></li>
          </ul>
          
          <p style="margin-left:.7pt"><span style="font-family:trebuchet ms,helvetica,sans-serif"><a href="https://www.gov.uk/guidance/apply-for-more-time-to-file-your-companys-accounts"><strong>https://www.gov.uk/guidance/apply-for-more-time-to-file-your-companys-accounts</strong></a></span></p>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>IR35 reform delayed</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Controversial IR35 tax reforms in UK delayed up to April 2021</span></li>
          </ul>
          
          <p>&nbsp;</p>
          
          <p><span style="color:#0000FF"><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong><u>BUDGET 2020</u></strong></span></span></p>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>National Insurance (NI) Relief</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">The Government has decided to increase the NI relief by 33% from &pound;3,000 to &pound;4,000. That is an additional &pound;1k of relief to your Employer&rsquo;s NIC contributions</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Ensure you claim this relief through your payroll</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>Entrepreneurs relief</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Is not abolished but the lifetime allowance is reduced from &pound;10m to &pound;1m, so will preserve the 10% rate on the first &pound;1 million of qualifying gains</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Business owners and their advisors will need to consider other options to reduce CGT on business sales in excess of this &pound;1m limit</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>National Insurance Contributions (NICs) </strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">National Insurance Threshold will rise to &pound;9,500 from April 2020 (was &pound;8,632). This will save on average c&pound;100 a year in for all employees and locums.</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">As a resident you don&rsquo;t have to do anything and just enjoy the additional c&pound;100</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">As a locum trading under a limited company, re-visit your salary drawdown to ensure maximum tax efficiency</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>Tampon tax</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">From 1st January 2021, the 5% &#39;tampon tax&#39; on women&rsquo;s sanitary products will be scrapped (zero-rated)</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>VAT</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">VAT registration limit (&pound;85,000) and deregistration limit (&pound;83,000) remains unchanged</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>Fuel duty</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Fuel duty froze for the 10th year in a row</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>Digital publications</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">No VAT to be charged on digital publications (e-books, e-newspapers, e-magazines and academic e-journals, etc.) from 1 December 2020</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>Income rate thresholds</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">The Income Tax thresholds for 2020-21 have remained unchanged at:</span>
          
              <ul>
                  <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Basic rate band &pound;37,500 (2019-20 &pound;37,500)</span></li>
                  <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Higher rate band &pound;37,501 to &pound;150,000 (2019-20 &pound;37,501 to &pound;150,000)</span></li>
                  <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Additional rate, no change, applies to income of more than &pound;150,000</span></li>
              </ul>
              </li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">There is no change in Income Tax rates, and the tax rates applied to dividend income</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Changes to these Income Tax bands apply to England, Wales and Northern Ireland. The Scottish parliament now set their own Income Tax bandings&nbsp;</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>Personal allowance</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">The personal Income Tax allowance for 2020-21 is maintained at &pound;12,500 (2019-20 &pound;12,500)</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>Corporation tax</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">The expected reduction of Corporation Tax to 17% has been cancelled</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Corporation Tax is to remain at 19%</span></li>
          </ul>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>ISA&rsquo;s</strong></span></p>
          
          <ul>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Adult savings limits remain unchanged at &pound;20,000</span></li>
              <li><span style="font-family:trebuchet ms,helvetica,sans-serif">Junior ISA limits are increased to &pound;9,000</span></li>
          </ul>
          
          <p>&nbsp;</p>
          
          <p><span style="font-family:trebuchet ms,helvetica,sans-serif"><strong>We at Locumkit will continue to keep you up to date with developments and how these can impact the people in the Optics industry. If you need any business advice or have a general question, please do not hesistate to contact us. </strong></span></p>', 'image_path' => '/media/files/industry_news/8c5ee609d771697ede76b3a3f7e42370_Budget-2020-scaled.jpg', 'user_type' => '2,3', 'category_id' => '8,3', 'status' => '1', 'created_at' => '2020-03-19 12:12:05', 'metatitle' => 'Budget 2020', 'metadescription' => 'The 2020 Spring Budget was heavily dominated by the issue of Coronavirus and of how to protect the economy against its effects.', 'metakeywords' => '')
        );

        IndustryNews::insert($industry_news);
    }
}