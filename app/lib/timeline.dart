import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:pulse_healthcare/logic/timeline_entry.dart';

class TimelinePage extends StatelessWidget {
  TimelinePage({Key key}) : super(key: key);

  final double timelineIconLeft = 15;
  final double timelineIconRadius = 20;
  final double timelineItemHeight = 180;

  @override
  Widget build(BuildContext context) {
    List<TimelineEntry> timelineEntries = TimelineEntry.getTimeline();

    return Container(
      child: ListView.builder(
        itemBuilder: (BuildContext context, int index) {
          return Stack(
            children: <Widget>[
              Padding(
                padding: EdgeInsets.only(
                  left: timelineIconLeft * 2 + timelineIconRadius,
                ),
                child: TimeLineCard(
                  timelineItemHeight: timelineItemHeight,
                  timelineEntry: timelineEntries[index],
                ),
              ),
              _buildTimeLineVLine(context),
              _buildTimeLineIcon(context)
            ],
          );
        },
        itemCount: timelineEntries.length,
      ),
    );
  }

  Widget _buildTimeLineIcon(BuildContext context) {
    return Positioned(
      top: timelineItemHeight / 2 - timelineIconRadius + 5.0,
      left: timelineIconLeft,
      child: Container(
        color: Theme.of(context).scaffoldBackgroundColor,
        padding: EdgeInsets.symmetric(vertical: 5.0),
        child: Container(
          width: 2 * timelineIconRadius,
          height: 2 * timelineIconRadius,
          decoration: BoxDecoration(
              color: Theme.of(context).primaryColor,
              borderRadius: BorderRadius.only(
                  bottomLeft: Radius.circular(16.0),
                  topLeft: Radius.circular(16.0))),
          child: Icon(
            FontAwesomeIcons.prescriptionBottleAlt,
            color: Colors.white,
          ),
        ),
      ),
    );
  }

  Widget _buildTimeLineVLine(BuildContext context) {
    return Positioned(
      top: 0.0,
      bottom: 0.0,
      left: timelineIconLeft + timelineIconRadius,
      child: Container(
        height: double.infinity,
        width: 2.0,
        color: Theme.of(context).primaryColor,
      ),
    );
  }
}

class TimeLineCard extends StatelessWidget {
  final double timelineItemHeight;
  final TimelineEntry timelineEntry;
  final List<String> months = [
    "JANUARY",
    "FEBRUARY",
    "MARCH",
    "APRIL",
    "MAY",
    "JUNE",
    "JULY",
    "AUGUST",
    "SEPTEMBER",
    "OCTOBER",
    "NOVEMBER",
    "DECEMBER"
  ];

  TimeLineCard({Key key, this.timelineItemHeight, this.timelineEntry})
      : super(key: key);

  @override
  Widget build(BuildContext context) {
    List<Widget> tags = [
      keywordChip("Med (${this.timelineEntry.medications})", context)
    ];
    if (this.timelineEntry.isImportant) {
      tags.add(keywordChip("Important", context));
    }
    if (this.timelineEntry.hasReport) {
      tags.add(keywordChip("Report", context));
    }

    return Container(
      decoration: BoxDecoration(
          border: Border.all(color: Theme.of(context).primaryColor),
          borderRadius: BorderRadius.circular(12.0)),
      width: double.infinity,
      height: timelineItemHeight + 2,
      margin: EdgeInsets.all(5.0),
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: <Widget>[
          Row(
            crossAxisAlignment: CrossAxisAlignment.baseline,
            textBaseline: TextBaseline.alphabetic,
            children: <Widget>[
              Text("${months[this.timelineEntry.date.month-1]} ${this.timelineEntry.date.day}",
                  style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900)),
              SizedBox(width: 10),
              Text("${this.timelineEntry.date.year}",
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
            ],
          ),
          ListTile(
            contentPadding: EdgeInsets.zero,
            leading: Icon(FontAwesomeIcons.userMd, color: Colors.black),
            title: Text("Dr. ${this.timelineEntry.doctorName}",
                style: TextStyle(
                    color: Colors.black, fontWeight: FontWeight.w700)),
            subtitle: Text("Prescribed doctor"),
          ),
          Wrap(
            alignment: WrapAlignment.start,
            spacing: 5.0,
            children: tags,
          )
        ],
      ),
    );
  }

  Widget keywordChip(String keyword, BuildContext context) {
    return Chip(
      padding: EdgeInsets.zero,
      label: Text(keyword, style: TextStyle(color: Colors.white)),
      backgroundColor: Theme.of(context).accentColor,
    );
  }
}
